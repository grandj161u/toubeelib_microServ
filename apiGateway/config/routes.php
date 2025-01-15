<?php

declare(strict_types=1);

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \gateway_tblb\application\actions\HomeAction::class);

    $app->get('/praticiens', \gateway_tblb\application\actions\GatewayListePraticienAction::class);

    $app->get('/praticiens/{idPraticien}', \gateway_tblb\application\actions\GatewayPraticienByIdAction::class);

    $app->get('/praticiens/{idPraticien}/rdvs', \gateway_tblb\application\actions\GatewayPlanningOuDispoPraticienAction::class);

    return $app;
};
