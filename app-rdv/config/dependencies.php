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
use api_rdv\application\actions\PlanningOuDispoPraticienAction;
use api_rdv\application\actions\PlanningPraticienAction;
use api_rdv\core\services\praticien\PraticienServiceInterface;
use api_rdv\infrastructure\adaptaters\PraticienServiceAdapter;
use PhpAmqpLib\Connection\AMQPStreamConnection;


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

        'guzzle.client.praticien' => function (ContainerInterface $c) {
            return new \GuzzleHttp\Client([
                'base_uri' => $c->get('settings')['praticien.api']
            ]);
        },

        'rabbitmq.connection.rdv' => function (ContainerInterface $c) {
            $connection = new AMQPStreamConnection(
                $c->get('settings')['rabbitmq.host'],
                $c->get('settings')['rabbitmq.port'],
                $c->get('settings')['rabbitmq.user'],
                $c->get('settings')['rabbitmq.password']
            );
            $channel = $connection->channel();
            $channel->exchange_declare(
                $c->get('settings')['rdv.event.exchange'],
                $c->get('settings')['rdv.type.exchange'],
                false,
                true,
                false
            );
            $channel->queue_declare(
                $c->get('settings')['rdv.queue'],
                false,
                true,
                false,
                false
            );
            $channel->queue_bind(
                $c->get('settings')['rdv.queue'],
                $c->get('settings')['rdv.event.exchange'],
                $c->get('settings')['rdv.routing.key']
            );
            return $connection;
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

        AnnulerRdvAction::class => function (ContainerInterface $c) {
            return new AnnulerRdvAction($c->get(ServiceRdvInterface::class));
        },

        PlanningOuDispoPraticienAction::class => function (ContainerInterface $c) {
            return new PlanningOuDispoPraticienAction($c->get(ServiceRdvInterface::class));
        },

        ServiceRdvInterface::class => function (ContainerInterface $c) {
            return new ServiceRdv(
                $c->get(RdvRepositoryInterface::class),
                $c->get(PraticienServiceInterface::class),
                $c->get('rabbitmq.connection.rdv')
            );
        },

        PraticienServiceInterface::class => function (ContainerInterface $c) {
            return new PraticienServiceAdapter($c->get('guzzle.client.praticien'));
        },

        RdvRepositoryInterface::class => function (ContainerInterface $c) {
            return new PDORdvRepository($c->get('rdv.pdo'), $c->get('patient.pdo'), $c->get('praticien.pdo'));
        },
    ];
