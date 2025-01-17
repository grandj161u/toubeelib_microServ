<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \api_auth\application\actions\HomeAction::class);

    $app->post('/users/signin', \api_auth\application\actions\SignInAction::class);

    $app->post('/refresh', \api_auth\application\actions\RefreshAction::class);

    $app->post('/register', \api_auth\application\actions\RegisterAction::class);

    $app->post('/tokens/validate', \api_auth\application\actions\ValidateTokenAction::class);

    return $app;
};
