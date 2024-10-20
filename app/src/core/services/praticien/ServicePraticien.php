<?php

namespace toubeelib\core\services\praticien;

use Respect\Validation\Exceptions\NestedValidationException;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\SpecialiteDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryDatabaseErrorException;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ServicePraticien implements ServicePraticienInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

    public function createPraticien(InputPraticienDTO $p): PraticienDTO
    {
        try {
            $praticien = new Praticien($p->__get('nom'), $p->__get('prenom'), $p->__get('tel'), $p->__get('adresse'));
            $praticien->setSpecialite($this->praticienRepository->getSpecialiteById($p->__get('specialite')));
            $id = $this->praticienRepository->save($praticien);
            $praticien->setID($id);
            return new PraticienDTO($praticien);
        } catch(NestedValidationException $e) {
            throw new ServicePraticienInvalidDataException('invalid Praticien data');
        } catch(RepositoryDatabaseErrorException $e) {
            throw new ServicePraticienInternalErrorException('Service internal Error' . $e->getMessage());
        }    
    }

    public function getPraticienById(string $id): PraticienDTO
    {
        try {
            $praticien = $this->praticienRepository->getPraticienById($id);
            return new PraticienDTO($praticien);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienInvalidDataException('invalid Praticien ID');
        } catch(RepositoryDatabaseErrorException $e) {
            throw new ServicePraticienInternalErrorException('Service internal Error' . $e->getMessage());
        } 
    }

    public function getSpecialiteById(string $id): SpecialiteDTO
    {
        try {
            $specialite = $this->praticienRepository->getSpecialiteById($id);
            return $specialite->toDTO();
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienInvalidDataException('invalid Specialite ID');
        } catch(RepositoryDatabaseErrorException $e) {
            throw new ServicePraticienInternalErrorException('Service internal Error' . $e->getMessage());
        } 
    }

    public function getAllPraticien(): array
    {
        try{
            $praticiens = $this->praticienRepository->getAllPraticien();
            $praticiensDTO = [];
                foreach($praticiens as $praticien){
                    $praticiensDTO[] = $praticien->toDTO();
                }
            return $praticiensDTO;
        } catch(RepositoryDatabaseErrorException $e) {
            throw new ServicePraticienInternalErrorException('Service internal Error' . $e->getMessage());
        } 
    }
}