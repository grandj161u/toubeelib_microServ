<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \api_praticien\application\actions\HomeAction::class);

    $app->post('/praticiens', \api_praticien\application\actions\CreerPraticienAction::class);

    $app->get('/praticiens', \api_praticien\application\actions\ListerPraticiensAction::class);

    $app->get('/praticiens/{idPraticien}', \api_praticien\application\actions\PraticienByIdAction::class);

    $app->get('/specialites/{idSpecialite}', \api_praticien\application\actions\SpecialiteByIdAction::class);

    $app->post('/users/signin', \api_praticien\application\actions\SignInAction::class);

    $app->post('/refresh', \api_praticien\application\actions\RefreshAction::class);

    return $app;
};
