<?php

declare(strict_types=1);

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \gateway_tblb\application\actions\HomeAction::class);

    $app->get('/praticiens', \gateway_tblb\application\actions\GatewayListePraticienAction::class);

    $app->get('/praticiens/{idPraticien}', \gateway_tblb\application\actions\GatewayPraticienByIdAction::class);

    $app->get('/praticiens/{idPraticien}/rdvs', \gateway_tblb\application\actions\GatewayPlanningOuDispoPraticienAction::class);

    $app->post('/users/signin', \gateway_tblb\application\actions\GatewaySignInAction::class);

    $app->post('/users/register', \gateway_tblb\application\actions\GatewayRegisterAction::class);

    $app->post('/users/refresh', \gateway_tblb\application\actions\GatewayRefreshAction::class);

    $app->post('/tokens/validate', \gateway_tblb\application\actions\GatewayValidateTokenAction::class);

    return $app;
};
