<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \api_praticien\application\actions\HomeAction::class);

    $app->post('/praticiens', \api_praticien\application\actions\CreerPraticienAction::class);

    $app->get('/praticiens', \api_praticien\application\actions\ListerPraticiensAction::class);

    $app->get('/praticiens/{idPraticien}', \api_praticien\application\actions\PraticienByIdAction::class)->add(\api_praticien\application\middlewares\AuthzPraticienMiddleware::class);

    $app->get('/specialites/{idSpecialite}', \api_praticien\application\actions\SpecialiteByIdAction::class);

    return $app;
};
