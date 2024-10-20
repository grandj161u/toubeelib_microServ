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

        if($inputRdvDTO->__get('horaire') < new \DateTimeImmutable('now')){
            throw new \Exception("La date et l'heure du rendez-vous ne peuvent pas être antérieures à la date et l'heure actuelles");
        }

        if($inputRdvDTO->__get('horaire')->format('H') < 8 || $inputRdvDTO->__get('horaire')->format('H') > 18){
            throw new \Exception("L'heure du rendez-vous doit être comprise entre 8h et 18h");
        }

        // // on vérifie que le rendez-vous n'est pas déjà pris et qu'il n'est pas compris dans un créneau de 30 minutes
        // $rdvs = $this->getRdvByPraticienId($inputRdvDTO->__get('idPraticien'));
        // $inputHoraire = $inputRdvDTO->__get('horaire');
        // foreach($rdvs as $rdv){
        //     $rdvHoraire = $rdv->__get('horaire');

        //     if($rdvHoraire->format('Y-m-d H:i:s') === $inputHoraire->format('Y-m-d H:i:s')){
        //         throw new \Exception("Le rendez-vous est déjà pris");
        //     }
            
        //     $startWindow = $rdvHoraire->sub(new DateInterval('PT30M'));
        //     $endWindow = $rdvHoraire->add(new DateInterval('PT30M'));
            
    
        //     if ($inputHoraire >= $startWindow && $inputHoraire <= $endWindow) {
        //         throw new \Exception("Le rendez-vous est compris dans un créneau de 30 minutes d'un autre rendez-vous");
        //     }  
        // }

        $rdv = $this->rdvRepository->creerRdv($inputRdvDTO->__get('idPraticien'), $inputRdvDTO->__get('idPatient'), $inputRdvDTO->__get('horaire'), $inputRdvDTO->__get('idSpecialite'), $inputRdvDTO->__get('type'), $inputRdvDTO->__get('statut'));
        $rdvDTO = $rdv->toDTO();
        return $rdvDTO;
    }

    public function annulerRdv(string $id): RdvDTO{
        try{

            if($this->rdvRepository->getRdvById($id)->__get('statut') === 'annule'){
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
        $rdvs = $this->rdvRepository->getRdvByPraticienId($idPraticien);
        
        $tabHorairePossible = [];

        //si la l'horaire de début commence avant 8h, on la fixe à 8h
        if($dateDebut->format('H') < 8){
            $dateDebut = new DateTimeImmutable($dateDebut->format('Y-m-d 08:00'));
        }

        //si la l'horaire de fin commence après 18h, on la fixe à 18h
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
    
        foreach ($rdvs as $rdv) {
            $tabHorairePrise[] = $rdv->__get('horaire')->format('Y-m-d H:i');
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