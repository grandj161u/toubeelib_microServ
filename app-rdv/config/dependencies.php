<?php

use Psr\Container\ContainerInterface;
use api_rdv\application\actions\ConsulterRdvAction;
use api_rdv\application\actions\ConsulterRdvByPatientAction;
use api_rdv\application\actions\CreerRdvAction;
use api_rdv\application\actions\ModifierOuGererCycleRdvAction;
use api_rdv\core\repositoryInterfaces\RdvRepositoryInterface;
use api_rdv\core\services\rdv\ServiceRdvInterface;
use api_rdv\core\services\rdv\ServiceRdv;
use api_rdv\infrastructure\repositories\PDORdvRepository;
use api_rdv\application\actions\DispoByPraticienAction;
use api_rdv\application\actions\AnnulerRdvAction;
use api_rdv\application\actions\PlanningPraticienAction;
use api_rdv\core\services\praticien\PraticienServiceInterface;
use api_rdv\infrastructure\adaptaters\PraticienServiceAdapter;


$settings = require __DIR__ . '/settings.php';

return
    [
        'settings' => $settings,

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

        'guzzle.client.praticien' => function (ContainerInterface $c) {
            return new \GuzzleHttp\Client([
                'base_uri' => $c->get('settings')['praticien.api']
            ]);
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
                $c->get(PraticienServiceInterface::class)
            );
        },

        PraticienServiceInterface::class => function (ContainerInterface $c) {
            return new PraticienServiceAdapter($c->get('guzzle.client.praticien'));
        },

        RdvRepositoryInterface::class => function (ContainerInterface $c) {
            return new PDORdvRepository($c->get('rdv.pdo'), $c->get('patient.pdo'), $c->get('praticien.pdo'));
        },
    ];
