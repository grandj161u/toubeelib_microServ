<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \gateway_tblb\application\actions\HomeAction::class);

    $app->get('/Praticiens', \gateway_tblb\application\actions\GatewayListePraticienAction::class);

    return $app;
};
