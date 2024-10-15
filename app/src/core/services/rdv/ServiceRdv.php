<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\dto\RdvDto;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\services\praticien\ServicePraticien;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\ModifyRdvDTO;

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

    public function creerRdv(InputRdvDTO $inputRdvDTO): RdvDTO{
        try{
            $praticien = $this->servicePraticien->getPraticienById($inputRdvDTO->__get('idPraticien'));

            $specialite = $this->servicePraticien->getSpecialiteById($inputRdvDTO->__get('idSpecialite'));

            if($this->servicePraticien->getPraticienById($inputRdvDTO->__get('idPraticien')));

            if($specialite->__get('label') !== $praticien->__get('specialite_label')){
                throw new ServiceRdvNotFoundException("Specialite label doesn't match" );
            }

            if($inputRdvDTO->__get('horaire') < new \DateTimeImmutable('now')){
                throw new \Exception("La date et l'heure du rendez-vous ne peuvent pas être antérieures à la date et l'heure actuelles");
            }

            $rdv = $this->rdvRepository->creerRdv($inputRdvDTO->__get('idPraticien'), $inputRdvDTO->__get('idPatient'), $inputRdvDTO->__get('horaire'), $inputRdvDTO->__get('idSpecialite'), $inputRdvDTO->__get('type'), $inputRdvDTO->__get('statut'));
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