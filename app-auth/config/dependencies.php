<?php

use Psr\Container\ContainerInterface;
use api_auth\application\actions\SignInAction;
use api_auth\core\repositoryInterfaces\AuthRepositoryInterface;
use api_auth\core\services\auth\ServiceAuthInterface;
use api_auth\infrastructure\repositories\PDOAuthRepository;
use api_auth\core\services\auth\ServiceAuth;
use api_auth\application\providers\auth\JWTManager;
use api_auth\application\providers\auth\JWTAuthProvider;
use api_auth\application\actions\RefreshAction;
use api_auth\application\actions\RegisterAction;
use api_auth\application\actions\ValidateTokenAction;
use api_auth\application\middlewares\AuthMiddleware;

return [

    'auth.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file(__DIR__ . '/auth.db.ini');
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
        $user = $config['username'];
        $password = $config['password'];
        return new PDO($dsn, $user, $password);
    },

    AuthRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthRepository($c->get('auth.pdo'));
    },

    JWTManager::class => function (ContainerInterface $c) {
        return new JWTManager(getenv('JWT_SECRET_KEY'), 'HS512');
    },

    ServiceAuthInterface::class => function (ContainerInterface $c) {
        return new ServiceAuth(
            $c->get(AuthRepositoryInterface::class),
            $c->get(JWTManager::class)
        );
    },

    JWTAuthProvider::class => function (ContainerInterface $c) {
        return new JWTAuthProvider($c->get(ServiceAuthInterface::class), $c->get(JWTManager::class));
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

    RegisterAction::class => function (ContainerInterface $c) {
        return new RegisterAction($c->get(JWTAuthProvider::class));
    },

    ValidateTokenAction::class => function (ContainerInterface $c) {
        return new ValidateTokenAction($c->get(JWTAuthProvider::class));
    }
];
