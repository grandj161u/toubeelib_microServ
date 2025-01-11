<?php

use Psr\Container\ContainerInterface;
use toubeelib\application\actions\ConsulterRdvAction;
use toubeelib\application\actions\ConsulterRdvByPatientAction;
use toubeelib\application\actions\CreerRdvAction;
use toubeelib\application\actions\ModifierOuGererCycleRdvAction;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\core\services\rdv\ServiceRdv;
use toubeelib\core\services\praticien\ServicePraticien;
use toubeelib\infrastructure\repositories\PDOPraticienRepository;
use toubeelib\infrastructure\repositories\PDORdvRepository;
use toubeelib\application\actions\ListerOuRechercherPraticienAction;
use toubeelib\application\actions\DispoByPraticienAction;
use toubeelib\application\actions\AnnulerRdvAction;
use toubeelib\application\actions\PlanningPraticienAction;
use toubeelib\application\actions\CreerPraticienAction;
use toubeelib\application\actions\ListerPraticiensAction;
use toubeelib\application\actions\PraticienByIdAction;
use toubeelib\application\actions\SignInAction;
use toubeelib\core\repositoryInterfaces\AuthRepositoryInterface;
use toubeelib\core\services\auth\ServiceAuthInterface;
use toubeelib\infrastructure\repositories\PDOAuthRepository;
use toubeelib\core\services\auth\ServiceAuth;
use toubeelib\application\providers\auth\JWTManager;
use toubeelib\application\providers\auth\JWTAuthProvider;
use toubeelib\application\actions\RefreshAction;
use toubeelib\application\middlewares\AuthMiddleware;
use toubeelib\core\domain\entities\praticien\Praticien;

return [

    'praticien.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/praticien.db.ini');
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        return new PDO($dsn, $user, $password);
    },

    'patient.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/patient.db.ini');
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        return new PDO($dsn, $user, $password);
    },

    'rdv.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/rdv.db.ini');
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        return new PDO($dsn, $user, $password);
    },

    'auth.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/auth.db.ini');
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        return new PDO($dsn, $user, $password);
    },

    ConsulterRdvByPatientAction::class => function (ContainerInterface $c) {
        return new ConsulterRdvByPatientAction($c->get(ServiceRdvInterface::class));
    },

    ConsulterRdvAction::class => function (ContainerInterface $c) {
        return new ConsulterRdvAction($c->get(ServiceRdvInterface::class));
    },

    ModifierOuGererCycleRdvAction::class => function (ContainerInterface $c) {
        return new ModifierOuGererCycleRdvAction($c->get(ServiceRdvInterface::class));
    },

    CreerRdvAction::class => function (ContainerInterface $c) {
        return new CreerRdvAction($c->get(ServiceRdvInterface::class));
    },

    DispoByPraticienAction::class => function (ContainerInterface $c) {
        return new DispoByPraticienAction($c->get(ServiceRdvInterface::class));
    },

    AnnulerRdvAction::class => function (ContainerInterface $c) {
        return new AnnulerRdvAction($c->get(ServiceRdvInterface::class));
    },

    PlanningPraticienAction::class => function (ContainerInterface $c) {
        return new PlanningPraticienAction($c->get(ServiceRdvInterface::class));
    },

    ServiceRdvInterface::class => function (ContainerInterface $c) {
        return new ServiceRdv(
            $c->get(RdvRepositoryInterface::class),
            $c->get(ServicePraticienInterface::class)
        );
    },

    RdvRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDORdvRepository($c->get('rdv.pdo'), $c->get('patient.pdo'), $c->get('praticien.pdo'));
    },
];
