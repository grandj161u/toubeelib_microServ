<?php

namespace api_praticien\core\services\praticien;

use Respect\Validation\Exceptions\NestedValidationException;
use api_praticien\core\domain\entities\praticien\Praticien;
use api_praticien\core\dto\InputPraticienDTO;
use api_praticien\core\dto\PraticienDTO;
use api_praticien\core\dto\SpecialiteDTO;
use api_praticien\core\repositoryInterfaces\PraticienRepositoryInterface;
use api_praticien\core\repositoryInterfaces\RepositoryDatabaseErrorException;
use api_praticien\core\repositoryInterfaces\RepositoryEntityNotFoundException;

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
        } catch (NestedValidationException $e) {
            throw new ServicePraticienInvalidDataException('invalid Praticien data');
        } catch (RepositoryDatabaseErrorException $e) {
            throw new ServicePraticienInternalErrorException('Service internal Error' . $e->getMessage());
        }
    }

    public function getPraticienById(string $id): PraticienDTO
    {
        try {
            $praticien = $this->praticienRepository->getPraticienById($id);
            return new PraticienDTO($praticien);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienNotFoundException('invalid Praticien ID');
        } catch (RepositoryDatabaseErrorException $e) {
            throw new ServicePraticienInternalErrorException('Service internal Error' . $e->getMessage());
        }
    }

    public function getSpecialiteById(string $id): SpecialiteDTO
    {
        try {
            $specialite = $this->praticienRepository->getSpecialiteById($id);
            return $specialite->toDTO();
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienNotFoundException('invalid Specialite ID');
        } catch (RepositoryDatabaseErrorException $e) {
            throw new ServicePraticienInternalErrorException('Service internal Error' . $e->getMessage());
        }
    }

    public function getAllPraticien(): array
    {
        try {
            $praticiens = $this->praticienRepository->getAllPraticien();
            $praticiensDTO = [];
            foreach ($praticiens as $praticien) {
                $praticiensDTO[] = $praticien->toDTO();
            }
            return $praticiensDTO;
        } catch (RepositoryDatabaseErrorException $e) {
            throw new ServicePraticienInternalErrorException('Service internal Error' . $e->getMessage());
        }
    }
}
