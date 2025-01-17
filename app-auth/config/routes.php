<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \toubeelib\application\actions\HomeAction::class);

    $app->post('/users/signin', \toubeelib\application\actions\SignInAction::class);

    $app->post('/refresh', \toubeelib\application\actions\RefreshAction::class);

    $app->post('/register', \toubeelib\application\actions\RegisterAction::class);

    return $app;
};
