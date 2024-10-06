<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\dto\RdvDto;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\services\praticien\ServicePraticien;
use toubeelib\core\services\praticien\ServicePraticienInterface;

class ServiceRdv implements ServiceRdvInterface {

    private RdvRepositoryInterface $rdvRepository;
    private ServicePraticienInterface $servicePraticien;

    public function __construct(RdvRepositoryInterface $rdvRepository, ServicePraticienInterface $servicePraticien) {
        $this->rdvRepository = $rdvRepository;
        $this->servicePraticien = $servicePraticien;
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

    public function modifierRdv(string $id, string|null $idSpecialite, string|null $idPatient): RdvDTO{
        try{
            $rdv = $this->rdvRepository->modifierRdv($id, $idSpecialite, $idPatient);
            $rdvDTO = $rdv->toDTO();
            return $rdvDTO;
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRdvNotFoundException("Rdv ID $id not found" );
        }
    }

    public function getRdvByPatient(string $id): array{
        $rdvs = $this->rdvRepository->getRdvByPatient($id);
        $rdvsDTO = [];
        foreach($rdvs as $rdv){
            $rdvsDTO[] = $rdv->toDTO();
        }
        return $rdvsDTO;
    }

    public function creerRdv(string $idPraticien, string $idPatient, \DateTimeImmutable $horaire, string $idSpecialite, string $type, string $statut){
        try{
            $praticien = $this->servicePraticien->getPraticienById($idPraticien);
            print_r($praticien);

            $specialite = $this->servicePraticien->getSpecialiteById($idSpecialite);
            print_r($specialite);

            if($this->servicePraticien->getPraticienById($idPraticien));

            if($specialite->__get('label') !== $praticien->__get('specialite_label')){
                throw new ServiceRdvNotFoundException("Specialite label doesn't match" );
            }

            if($horaire < new \DateTimeImmutable('now')){
                throw new \Exception("La date et l'heure du rendez-vous ne peuvent pas être antérieures à la date et l'heure actuelles");
            }

            $rdv = $this->rdvRepository->creerRdv($idPraticien, $idPatient, $horaire, $idSpecialite, $type, $statut);
            $rdvDTO = $rdv->toDTO();
            return $rdvDTO;
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRdvNotFoundException("Rdv ID not found" );
        }
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

}