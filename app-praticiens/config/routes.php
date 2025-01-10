<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \toubeelib\application\actions\HomeAction::class);

    $app->post('/praticiens', \toubeelib\application\actions\CreerPraticienAction::class);

    $app->get('/praticiens', \toubeelib\application\actions\ListerPraticiensAction::class);

    $app->get('/praticiens/{idPraticien}', \toubeelib\application\actions\PraticienByIdAction::class);

    $app->post('/users/signin', \toubeelib\application\actions\SignInAction::class);

    $app->post('/refresh', \toubeelib\application\actions\RefreshAction::class);

    return $app;
};
