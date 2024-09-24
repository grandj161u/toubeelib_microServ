<?php

use Faker\Container\ContainerInterface;
use toubeelib\application\actions\ConsulterRdvAction;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\core\services\rdv\ServiceRdv;

return [

    ConsulterRdvAction::class => function (ContainerInterface $c) {
        return new ConsulterRdvAction($c->get(ServiceRdvInterface::class));
    },

    ServiceRdvInterface::class => function (ContainerInterface $c) {
        return new ServiceRdv($c->get(ServicePraticienInterface::class), 
                                            $c->get(RdvRepositoryInterface::class));
    }
];