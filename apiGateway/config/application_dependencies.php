<?php

use gateway_tblb\application\actions\GatewayListePraticienAction;
use gateway_tblb\application\actions\HomeAction;
use Psr\Container\ContainerInterface;

$settings = require __DIR__ . '/settings.php';

return
    [
        'settings' => $settings,
        
        'guzzle.client.api' => function (ContainerInterface $c) {
            return new \GuzzleHttp\Client([
                'base_uri' => $c->get('settings')['toubeelib.praticiens.api']
            ]);
        },

        GatewayListePraticienAction::class => function (ContainerInterface $c) {
            return new GatewayListePraticienAction($c->get('guzzle.client.api'));
        },

        HomeAction::class => function (ContainerInterface $c) {
            return new HomeAction($c->get('guzzle.client.api'));
        }
    ];
