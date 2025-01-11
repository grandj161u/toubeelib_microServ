<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \toubeelib\application\actions\HomeAction::class);

    $app->get('/rdvs/{id}', \toubeelib\application\actions\ConsulterRdvAction::class);

    $app->patch('/rdvs/{id}', \toubeelib\application\actions\ModifierOuGererCycleRdvAction::class);

    $app->get('/patients/{idPatient}/rdvs', \toubeelib\application\actions\ConsulterRdvByPatientAction::class);

    $app->post('/rdvs', \toubeelib\application\actions\CreerRdvAction::class);

    $app->delete('/rdvs/{id}', \toubeelib\application\actions\AnnulerRdvAction::class);

    // l'url s'utilise de cette manière : /praticiens/{idPraticien}/dispos?debut=2024-10-19&fin=2024-10-20
    $app->get('/praticiens/{idPraticien}/dispos', \toubeelib\application\actions\DispoByPraticienAction::class);

    // l'url s'utilise de cette manière : /praticiens/{idPraticien}/rdvs?debut=2024-10-19&fin=2024-10-20
    $app->get('/praticiens/{idPraticien}/rdvs', \toubeelib\application\actions\PlanningPraticienAction::class);

    return $app;
};
