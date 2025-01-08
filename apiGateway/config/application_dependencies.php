<?php

use gateway_tblb\application\actions\AbstractGatewayAction;
use gateway_tblb\application\actions\GatewayListePraticienAction;
use Psr\Container\ContainerInterface;

return
    [
        'guzzle.client.api' => function (ContainerInterface $c) {
            return new \GuzzleHttp\Client([
                'base_uri' => $c->get('settings')['toubeelib.praticiens.api']
            ]);
        },

        GatewayListePraticienAction::class => function (ContainerInterface $c) {
            return new AbstractGatewayAction($c->get('guzzle.client.api'));
        },
    ];
