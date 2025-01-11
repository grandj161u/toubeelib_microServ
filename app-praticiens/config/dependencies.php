<?php

use Psr\Container\ContainerInterface;
use api_praticien\core\repositoryInterfaces\PraticienRepositoryInterface;
use api_praticien\core\services\praticien\ServicePraticienInterface;
use api_praticien\core\services\praticien\ServicePraticien;
use api_praticien\infrastructure\repositories\PDOPraticienRepository;
use api_praticien\application\actions\CreerPraticienAction;
use api_praticien\application\actions\ListerPraticiensAction;
use api_praticien\application\actions\PraticienByIdAction;
use api_praticien\application\actions\SignInAction;
use api_praticien\core\repositoryInterfaces\AuthRepositoryInterface;
use api_praticien\core\services\auth\ServiceAuthInterface;
use api_praticien\infrastructure\repositories\PDOAuthRepository;
use api_praticien\core\services\auth\ServiceAuth;
use api_praticien\application\providers\auth\JWTManager;
use api_praticien\application\providers\auth\JWTAuthProvider;
use api_praticien\application\actions\RefreshAction;
use api_praticien\application\middlewares\AuthMiddleware;
use api_praticien\application\actions\SpecialiteByIdAction;

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

    'auth.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/auth.db.ini');
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        return new PDO($dsn, $user, $password);
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

    AuthRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthRepository($c->get('auth.pdo'));
    },

    ServiceAuthInterface::class => function (ContainerInterface $c) {
        return new ServiceAuth(
            $c->get(AuthRepositoryInterface::class),
            $c->get(JWTManager::class)
        );
    },

    JWTAuthProvider::class => function (ContainerInterface $c) {
        return new JWTAuthProvider($c->get(ServiceAuth::class), $c->get(JWTManager::class));
    },

    JWTManager::class => function (ContainerInterface $c) {
        return new JWTManager(getenv('JWT_SECRET_KEY'), 'HS512');
    },

    SignInAction::class => function (ContainerInterface $c) {
        return new SignInAction($c->get(JWTAuthProvider::class));
    },

    RefreshAction::class => function (ContainerInterface $c) {
        return new RefreshAction($c->get(JWTAuthProvider::class));
    },

    AuthMiddleware::class => function (ContainerInterface $c) {
        return new AuthMiddleware($c->get(JWTAuthProvider::class));
    },

    SpecialiteByIdAction::class => function (ContainerInterface $c) {
        return new SpecialiteByIdAction($c->get(ServicePraticienInterface::class));
    }
];
