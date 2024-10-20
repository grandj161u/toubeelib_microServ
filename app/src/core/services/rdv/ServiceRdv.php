<?php

namespace toubeelib\core\services\rdv;

use DateInterval;
use toubeelib\core\dto\RdvDto;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\services\praticien\ServicePraticien;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\ModifyRdvDTO;
use toubeelib\core\dto\GererCycleRdvDTO;
use DateTimeImmutable;

class ServiceRdv implements ServiceRdvInterface {

    private RdvRepositoryInterface $rdvRepository;
    private ServicePraticienInterface $servicePraticien;

    public function __construct(RdvRepositoryInterface $rdvRepository, ServicePraticienInterface $servicePraticien) {
        $this->rdvRepository = $rdvRepository;
        $this->servicePraticien = $servicePraticien;
    }

    public function getRdvs(): array{
        $rdvs = $this->rdvRepository->getRdvs();
        $rdvsDTO = [];
        foreach($rdvs as $rdv){
            $rdvsDTO[] = $rdv->toDTO();
        }
        return $rdvsDTO;
    }


    public function getRdvById(string $id): RdvDTO{
        try{
            $rdv = $this->rdvRepository->getRdvById($id);
            $rdvDTO = $rdv->toDTO();
            return $rdvDTO;
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRdvNotFoundException("Rdv ID $id not found" );
        }
    }

    public function modifierRdv(ModifyRdvDTO $modifyRdvDTO, String $ID): RdvDTO{
        $rdv = $this->rdvRepository->modifierRdv($ID, $modifyRdvDTO->__get('idSpecialite'), $modifyRdvDTO->__get('idPatient'));
        $rdvDTO = $rdv->toDTO();
        return $rdvDTO;
    }

    public function GererCycleRdv(GererCycleRdvDTO $gererCycleRdvDTO, string $ID): RdvDTO{
        $rdv = $this->rdvRepository->GererCycleRdv($ID, $gererCycleRdvDTO->__get('statut'));
        $rdvDTO = $rdv->toDTO();
        return $rdvDTO;
    }

    public function getRdvByPatient(string $id): array{
        $rdvs = $this->rdvRepository->getRdvByPatient($id);
        $rdvsDTO = [];
        foreach($rdvs as $rdv){
            $rdvsDTO[] = $rdv->toDTO();
        }
        return $rdvsDTO;
    }

    public function getRdvByPraticienId(string $id): array{
        $rdvs = $this->rdvRepository->getRdvByPraticienId($id);
        $rdvsDTO = [];
        foreach($rdvs as $rdv){
            $rdvsDTO[] = $rdv->toDTO();
        }
        return $rdvsDTO;
    }

    public function creerRdv(InputRdvDTO $inputRdvDTO): RdvDTO{
        $praticien = $this->servicePraticien->getPraticienById($inputRdvDTO->__get('idPraticien'));

        $specialite = $this->servicePraticien->getSpecialiteById($inputRdvDTO->__get('idSpecialite'));

        if($this->servicePraticien->getPraticienById($inputRdvDTO->__get('idPraticien')));

        if($specialite->__get('label') !== $praticien->__get('specialite_label')){
            throw new ServiceRdvNotFoundException("Specialite label doesn't match" );
        }

        $horaire = $inputRdvDTO->__get('horaire');

        if($horaire < new DateTimeImmutable('now')){
            throw new \Exception("La date et l'heure du rendez-vous ne peuvent pas être antérieures à la date et l'heure actuelles");
        }

        if($horaire->format('H') < 8 || $horaire->format('H') > 18){
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
        $inputRdvDTO->__get('statut'));
        $rdvDTO = $rdv->toDTO();
        return $rdvDTO;
    }

    public function annulerRdv(string $id): RdvDTO{
        try{

            if($this->rdvRepository->getRdvById($id)->__get('statut') === 'annuler'){
                throw new \Exception("Le rendez-vous a déjà été annulé");
            }
            
            $rdv = $this->rdvRepository->annulerRdv($id);
            $rdvDTO = $rdv->toDTO();
            return $rdvDTO;
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRdvNotFoundException("Rdv ID not found" );
        }
    }

    public function getDisponibiliterPraticien(string $idPraticien, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): array {

        try{
        $rdvs = $this->rdvRepository->getRdvByPraticienId($idPraticien);
        } catch(RepositoryEntityNotFoundException $e) {
            $rdvs = [];
        }
        
        $tabHorairePossible = [];

        //si l'horaire de début commence avant 8h, on la fixe à 8h
        if($dateDebut->format('H') < 8){
            $dateDebut = new DateTimeImmutable($dateDebut->format('Y-m-d 08:00'));
        }

        //si l'horaire de fin commence après 18h, on la fixe à 18h
        if($dateFin->format('H') > 18){
            $dateFin = new DateTimeImmutable($dateFin->format('Y-m-d 18:00'));
        }
        $startTime = $dateDebut;
        $endTime = $dateFin;
    
        while ($startTime <= $endTime) {
            $tabHorairePossible[] = $startTime->format('Y-m-d H:i');
            $startTime = $startTime->add(new DateInterval('PT30M'));
        }

        $tabHorairePrise = [];
        
        if (!empty($rdvs)) {
            foreach ($rdvs as $rdv) {
                if($rdv->__get('statut') !== 'annule'){
                    $tabHorairePrise[] = $rdv->__get('horaire')->format('Y-m-d H:i');
                }
            }
        }

        $disponibilites = array_diff($tabHorairePossible, $tabHorairePrise);

        foreach($disponibilites as $key => $dispo){
            $disponibilites[$key] = new DateTimeImmutable($dispo);
            if($disponibilites[$key]->format('H') < 8 || $disponibilites[$key]->format('H') >= 18){
                unset($disponibilites[$key]);
            }
        }
    
        return array_values($disponibilites);
    }

}