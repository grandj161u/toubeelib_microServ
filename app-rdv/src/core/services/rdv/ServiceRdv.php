<?php

namespace api_rdv\core\services\rdv;

use DateInterval;
use api_rdv\core\dto\RdvDto;
use api_rdv\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use api_rdv\core\repositoryInterfaces\RdvRepositoryInterface;
use api_rdv\core\dto\InputRdvDTO;
use api_rdv\core\dto\ModifyRdvDTO;
use api_rdv\core\dto\GererCycleRdvDTO;
use DateTimeImmutable;
use api_rdv\core\services\praticien\PraticienServiceInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ServiceRdv implements ServiceRdvInterface
{

    private RdvRepositoryInterface $rdvRepository;
    private PraticienServiceInterface $servicePraticien;
    private AMQPStreamConnection $connection;

    public function __construct(RdvRepositoryInterface $rdvRepository, PraticienServiceInterface $praticienService, AMQPStreamConnection $connection)
    {
        $this->rdvRepository = $rdvRepository;
        $this->servicePraticien = $praticienService;
        $this->connection = $connection;
    }

    public function getRdvs(): array
    {
        $rdvs = $this->rdvRepository->getRdvs();
        $rdvsDTO = [];
        foreach ($rdvs as $rdv) {
            $rdvsDTO[] = $rdv->toDTO();
        }
        return $rdvsDTO;
    }


    public function getRdvById(string $id): RdvDTO
    {
        try {
            $rdv = $this->rdvRepository->getRdvById($id);
            $rdvDTO = $rdv->toDTO();
            return $rdvDTO;
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRdvNotFoundException("Rdv ID $id not found");
        }
    }

    public function modifierRdv(ModifyRdvDTO $modifyRdvDTO, String $ID): RdvDTO
    {
        $rdv = $this->rdvRepository->modifierRdv($ID, $modifyRdvDTO->__get('idSpecialite'), $modifyRdvDTO->__get('idPatient'));
        $rdvDTO = $rdv->toDTO();
        return $rdvDTO;
    }

    public function GererCycleRdv(GererCycleRdvDTO $gererCycleRdvDTO, string $ID): RdvDTO
    {
        $rdv = $this->rdvRepository->GererCycleRdv($ID, $gererCycleRdvDTO->__get('statut'));
        $rdvDTO = $rdv->toDTO();
        return $rdvDTO;
    }

    public function getRdvByPatient(string $id): array
    {
        $rdvs = $this->rdvRepository->getRdvByPatient($id);
        $rdvsDTO = [];
        foreach ($rdvs as $rdv) {
            $rdvsDTO[] = $rdv->toDTO();
        }
        return $rdvsDTO;
    }

    public function getRdvByPraticienId(string $id): array
    {
        $rdvs = $this->rdvRepository->getRdvByPraticienId($id);
        $rdvsDTO = [];
        foreach ($rdvs as $rdv) {
            $rdvsDTO[] = $rdv->toDTO();
        }
        return $rdvsDTO;
    }

    public function creerRdv(InputRdvDTO $inputRdvDTO): RdvDTO
    {
        $praticien = $this->servicePraticien->getPraticienById($inputRdvDTO->__get('idPraticien'));

        $specialite = $this->servicePraticien->getSpecialiteById($inputRdvDTO->__get('idSpecialite'));

        if ($this->servicePraticien->getPraticienById($inputRdvDTO->__get('idPraticien')));

        if ($specialite->__get('label') !== $praticien->__get('specialite')->__get('label')) {
            throw new ServiceRdvNotFoundException("Specialite doesn't match");
        }

        $horaire = $inputRdvDTO->__get('horaire');

        if ($horaire < new DateTimeImmutable('now')) {
            throw new \Exception("La date et l'heure du rendez-vous ne peuvent pas être antérieures à la date et l'heure actuelles");
        }

        if ($horaire->format('H') < 8 || $horaire->format('H') > 18) {
            throw new \Exception("L'heure du rendez-vous doit être comprise entre 8h et 18h");
        }

        $dateDebut = new DateTimeImmutable($horaire->format('Y-m-d 08:00'));
        $dateFin = new DateTimeImmutable($horaire->format('Y-m-d 18:00'));

        $disponibilites = $this->getDisponibiliterPraticien($inputRdvDTO->__get('idPraticien'), $dateDebut, $dateFin);

        $horaireChoisi = $horaire->format('Y-m-d H:i');
        $dispoTrouvee = false;

        foreach ($disponibilites as $dispo) {
            if ($dispo->format('Y-m-d H:i') === $horaireChoisi) {
                $dispoTrouvee = true;
                break;
            }
        }

        if (!$dispoTrouvee) {
            throw new \Exception("Le praticien n'est pas disponible à cet horaire");
        }

        $rdv = $this->rdvRepository->creerRdv(
            $inputRdvDTO->__get('idPraticien'),
            $inputRdvDTO->__get('idPatient'),
            $horaire,
            $inputRdvDTO->__get('idSpecialite'),
            $inputRdvDTO->__get('type'),
            $inputRdvDTO->__get('statut')
        );
        $rdvDTO = $rdv->toDTO();
        return $rdvDTO;
    }

    public function annulerRdv(string $id): RdvDTO
    {
        try {

            if ($this->rdvRepository->getRdvById($id)->__get('statut') === 'annuler') {
                throw new \Exception("Le rendez-vous a déjà été annulé");
            }

            $rdv = $this->rdvRepository->annulerRdv($id);
            $rdvDTO = $rdv->toDTO();
            return $rdvDTO;
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRdvNotFoundException("Rdv ID not found");
        }
    }

    public function getDisponibiliterPraticien(string $idPraticien, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): array
    {

        try {
            $rdvs = $this->rdvRepository->getRdvByPraticienId($idPraticien);
        } catch (RepositoryEntityNotFoundException $e) {
            if ($this->servicePraticien->getPraticienById($idPraticien)) {
                $rdvs = [];
            } else {
                throw new ServiceRdvNotFoundException("Praticien ID not found");
            }
        }

        $tabHorairePossible = [];

        $startTime = $dateDebut;
        $endTime = $dateFin;

        while ($startTime <= $endTime) {
            $tabHorairePossible[] = $startTime->format('Y-m-d H:i');
            $startTime = $startTime->add(new DateInterval('PT30M'));
        }

        $tabHorairePrise = [];

        if (!empty($rdvs)) {
            foreach ($rdvs as $rdv) {
                if ($rdv->__get('statut') !== 'annuler') {
                    $tabHorairePrise[] = $rdv->__get('horaire')->format('Y-m-d H:i');
                }
            }
        }

        $disponibilites = array_diff($tabHorairePossible, $tabHorairePrise);

        foreach ($disponibilites as $key => $dispo) {
            $disponibilites[$key] = new DateTimeImmutable($dispo);
            if ($disponibilites[$key]->format('H') < 8 || $disponibilites[$key]->format('H') >= 18) {
                unset($disponibilites[$key]);
            }
        }

        return array_values($disponibilites);
    }

    public function getPlanningPraticien(string $idPraticien, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): array
    {

        try {
            $rdvs = $this->rdvRepository->getRdvByPraticienId($idPraticien);
        } catch (RepositoryEntityNotFoundException $e) {
            if ($this->servicePraticien->getPraticienById($idPraticien)) {
                $rdvs = [];
            } else {
                throw new ServiceRdvNotFoundException("Praticien ID not found");
            }
        }

        $planning = [];

        foreach ($rdvs as $rdv) {
            $horaireRdv = $rdv->__get('horaire');

            if ($horaireRdv >= $dateDebut && $horaireRdv <= $dateFin && $rdv->__get('statut') !== 'annuler') {
                $planning[] = [
                    'horaire' => $horaireRdv->format('Y-m-d H:i'),
                    'specialitée' => $this->servicePraticien->getSpecialiteById($rdv->__get('idSpecialite'))->__get('label'),
                    'type' => $rdv->__get('type'),
                ];
            }
        }

        return $planning;
    }

    public function sendMessageRdv($message, $idRdv)
    {
        $channel = $this->connection->channel();

        $rdv = $this->getRdvById($idRdv);

        $message = [
            'message' => $message,
            'IdRdv' => $rdv->ID,
            'IdPraticien' => $rdv->idPraticien,
            'IdPatient' => $rdv->idPatient,
        ];

        $jsonMessage = json_encode($message, JSON_THROW_ON_ERROR);

        $msg = new \PhpAmqpLib\Message\AMQPMessage($jsonMessage);

        $channel->basic_publish($msg, 'rdv.exchange', 'rdv.key');
        $channel->close();
        $this->connection->close();
    }
}
