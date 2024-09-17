<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\dto\RdvDto;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;

class ServiceRdv implements ServiceRdvInterface {

    private RdvRepositoryInterface $rdvRepository;

    public function __construct(RdvRepositoryInterface $rdvRepository)
    {
        $this->rdvRepository = $rdvRepository;
    }


    public function getRdvById(string $id): RdvDTO{
        try{
            $rdv = $this->rdvRepository->getRdvById($id);
            return new RdvDTO($rdv);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRdvInvalidDataException('invalid rdv ID');
        }
    }

}