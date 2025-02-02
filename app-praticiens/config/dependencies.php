<?php

use Psr\Container\ContainerInterface;
use api_praticien\core\repositoryInterfaces\PraticienRepositoryInterface;
use api_praticien\core\services\praticien\ServicePraticienInterface;
use api_praticien\core\services\praticien\ServicePraticien;
use api_praticien\infrastructure\repositories\PDOPraticienRepository;
use api_praticien\application\actions\CreerPraticienAction;
use api_praticien\application\actions\ListerPraticiensAction;
use api_praticien\application\actions\PraticienByIdAction;
use api_praticien\application\actions\SpecialiteByIdAction;
use api_praticien\application\middlewares\AuthzPraticienMiddleware;
use api_praticien\core\services\praticien\authorization\AuthzPraticienServiceInterface;
use api_praticien\core\services\praticien\authorization\AuthzPraticienService;
use api_praticien\infrastructure\adaptaters\AuthServiceAdapter;
use api_praticien\core\services\auth\ServiceAuthInterface;

$settings = require __DIR__ . '/settings.php';

return [

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

    'guzzle.client.auth' => function (ContainerInterface $c) {
        return new \GuzzleHttp\Client([
            'base_uri' => $c->get('settings')['auth.api']
        ]);
    },

    ListerPraticiensAction::class => function (ContainerInterface $c) {
        return new ListerPraticiensAction($c->get(ServicePraticienInterface::class));
    },

    PraticienByIdAction::class => function (ContainerInterface $c) {
        return new PraticienByIdAction($c->get(ServicePraticienInterface::class));
    },

    CreerPraticienAction::class => function (ContainerInterface $c) {
        return new CreerPraticienAction($c->get(ServicePraticienInterface::class));
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticien($c->get(PraticienRepositoryInterface::class));
    },

    PraticienRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPraticienRepository($c->get('praticien.pdo'));
    },

    SpecialiteByIdAction::class => function (ContainerInterface $c) {
        return new SpecialiteByIdAction($c->get(ServicePraticienInterface::class));
    },

    AuthzPraticienServiceInterface::class => function (ContainerInterface $c) {
        return new AuthzPraticienService();
    },

    AuthzPraticienMiddleware::class => function (ContainerInterface $c) {
        return new AuthzPraticienMiddleware($c->get(AuthzPraticienServiceInterface::class), $c->get(ServiceAuthInterface::class));
    },

    ServiceAuthInterface::class => function (ContainerInterface $c) {
        return new AuthServiceAdapter($c->get('guzzle.client.auth'));
    },
];
