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

    $app->post('/praticiens', \toubeelib\application\actions\CreerPraticienAction::class);

    $app->delete('/rdvs/{id}', \toubeelib\application\actions\AnnulerRdvAction::class);

    // l'url s'utilise de cette manière : /DispoPraticien/{idPraticien}/2024-10-19/2024-10-20 et si vous voulez rajouter l'heure : /DispoPraticien/{idPraticien}/2024-10-19 16:00/2024-10-20 18:00
    $app->get('/DispoPraticien/{idPraticien}/{dateDebut}/{dateFin}', \toubeelib\application\actions\DispoByPraticienAction::class);

    // l'url s'utilise de cette manière : /praticiens/{idPraticien}/rdvs?debut=2024-10-19&fin=2024-10-20 et si vous voulez rajouter l'heure : /praticiens/{idPraticien}/rdvs?debut=2024-10-19 16:00&fin=2024-10-20 18:00
    $app->get('/praticiens/{idPraticien}/rdvs', \toubeelib\application\actions\PlanningPraticienAction::class);

    $app->get('/praticiens', \toubeelib\application\actions\ListerPraticiensAction::class);

    $app->get('/praticiens/{idPraticien}', \toubeelib\application\actions\PraticienByIdAction::class);

    $app->post('/users/signin', \toubeelib\application\actions\SignInAction::class);

    $app->post('/refresh', \toubeelib\application\actions\RefreshAction::class);

    return $app;
};
