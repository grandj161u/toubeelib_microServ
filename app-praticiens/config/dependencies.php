<?php

use Psr\Container\ContainerInterface;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\praticien\ServicePraticien;
use toubeelib\infrastructure\repositories\PDOPraticienRepository;
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
    }
];
