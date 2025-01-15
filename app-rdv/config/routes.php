<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \api_rdv\application\actions\HomeAction::class);

    $app->get('/rdvs/{id}', \api_rdv\application\actions\ConsulterRdvAction::class);

    $app->patch('/rdvs/{id}', \api_rdv\application\actions\ModifierOuGererCycleRdvAction::class);

    $app->get('/patients/{idPatient}/rdvs', \api_rdv\application\actions\ConsulterRdvByPatientAction::class);

    $app->post('/rdvs', \api_rdv\application\actions\CreerRdvAction::class);

    $app->delete('/rdvs/{id}', \api_rdv\application\actions\AnnulerRdvAction::class);

    // Soit on veut le planning du praticien
    // l'url s'utilise de cette manière : /praticiens/{idPraticien}/rdvs?statut=planning&debut=2024-10-19&fin=2024-10-20

    // Soit on veut les disponibilités du praticien
    // l'url s'utilise de cette manière : /praticiens/{idPraticien}/rdvs?statut=dispo&debut=2024-10-19&fin=2024-10-20
    $app->get('/praticiens/{idPraticien}/rdvs', \api_rdv\application\actions\PlanningOuDispoPraticienAction::class);

    return $app;
};
