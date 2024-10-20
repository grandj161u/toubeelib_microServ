<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function( \Slim\App $app):\Slim\App {

    $app->get('/', \toubeelib\application\actions\HomeAction::class);

    $app->get('/Rdvs/{id}', \toubeelib\application\actions\ConsulterRdvAction::class);

    $app->patch('/Rdvs/{id}', \toubeelib\application\actions\ModifierRdvAction::class);

    $app->get('/Rdvs/patient/{idPatient}', \toubeelib\application\actions\ConsulterRdvByPatientAction::class);

    $app->post('/Rdvs', \toubeelib\application\actions\CreerRdvAction::class);

    $app->delete('/Rdvs/{id}', \toubeelib\application\actions\AnnulerRdvAction::class);

    // l'url s'utilise de cette manière : /DispoPraticien/{idPraticien}/2024-10-19/2024-10-20 et si vous voulez rajouter l'heure : /DispoPraticien/{idPraticien}/2024-10-19 16:00/2024-10-20 18:00
    $app->get('/DispoPraticien/{idPraticien}/{dateDebut}/{dateFin}', \toubeelib\application\actions\DispoByPraticien::class);


    return $app;
};