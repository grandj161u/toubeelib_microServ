<?php

use Psr\Container\ContainerInterface;
use toubeelib\application\actions\ConsulterRdvAction;
use toubeelib\application\actions\ConsulterRdvByPatientAction;
use toubeelib\application\actions\CreerRdvAction;
use toubeelib\application\actions\ModifierRdvAction;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\infrastructure\repositories\ArrayPraticienRepository;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;
use toubeelib\core\services\praticien\ServicePraticien;


return [

    'praticien.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/praticien.db.ini');
        $dsn = "{config['driver']}:host={config['host']};dbname={config['database']}";
        $user = $config['user'];
        $password = $config['password'];
        return new PDO($dsn, $user, $password);
    },

    ConsulterRdvByPatientAction::class => function (ContainerInterface $c) {
        return new ConsulterRdvByPatientAction($c->get(ServiceRdvInterface::class));
    },

    ConsulterRdvAction::class => function (ContainerInterface $c) {
        return new ConsulterRdvAction($c->get(ServiceRdvInterface::class));
    },

    ModifierRdvAction::class => function (ContainerInterface $c) {
        return new ModifierRdvAction($c->get(ServiceRdvInterface::class));
    },

    CreerRdvAction::class => function (ContainerInterface $c) {
        return new CreerRdvAction($c->get(ServiceRdvInterface::class));
    },

    ServiceRdvInterface::class => function (ContainerInterface $c) {
        return new ServiceRdv(
        $c->get(RdvRepositoryInterface::class), 
        $c->get(ServicePraticienInterface::class));
    },

    RdvRepositoryInterface::class => function (ContainerInterface $c) {
        return new ArrayRdvRepository();
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticien($c->get(PraticienRepositoryInterface::class));
    },

    PraticienRepositoryInterface::class => function (ContainerInterface $c) {
        return new ArrayPraticienRepository();
    }

];