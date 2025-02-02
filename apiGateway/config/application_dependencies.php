<?php

use gateway_tblb\application\actions\GatewayAnnulerRdvAction;
use gateway_tblb\application\actions\GatewayCreerRdvAction;
use gateway_tblb\application\actions\GatewayListePraticienAction;
use gateway_tblb\application\actions\GatewayPlanningOuDispoPraticienAction;
use gateway_tblb\application\actions\GatewayPraticienByIdAction;
use gateway_tblb\application\actions\GatewayRefreshAction;
use gateway_tblb\application\actions\GatewayRegisterAction;
use gateway_tblb\application\actions\GatewaySignInAction;
use gateway_tblb\application\actions\GatewayValidateTokenAction;
use gateway_tblb\application\middlewares\GatewayAuthnMiddleware;
use Psr\Container\ContainerInterface;

$settings = require __DIR__ . '/settings.php';

return
    [
        'settings' => $settings,

        'guzzle.client.api' => function (ContainerInterface $c) {
            return new \GuzzleHttp\Client([
                'base_uri' => $c->get('settings')['toubeelib.api']
            ]);
        },

        'guzzle.client.praticien' => function (ContainerInterface $c) {
            return new \GuzzleHttp\Client([
                'base_uri' => $c->get('settings')['praticien.api']
            ]);
        },

        'guzzle.client.rdv' => function (ContainerInterface $c) {
            return new \GuzzleHttp\Client([
                'base_uri' => $c->get('settings')['rdv.api']
            ]);
        },

        'guzzle.client.auth' => function (ContainerInterface $c) {
            return new \GuzzleHttp\Client([
                'base_uri' => $c->get('settings')['auth.api']
            ]);
        },

        GatewayListePraticienAction::class => function (ContainerInterface $c) {
            return new GatewayListePraticienAction($c->get('guzzle.client.praticien'));
        },

        GatewayPraticienByIdAction::class => function (ContainerInterface $c) {
            return new GatewayPraticienByIdAction(($c->get('guzzle.client.praticien')));
        },

        GatewayPlanningOuDispoPraticienAction::class => function (ContainerInterface $c) {
            return new GatewayPlanningOuDispoPraticienAction($c->get('guzzle.client.rdv'));
        },

        GatewayCreerRdvAction::class => function (ContainerInterface $c) {
            return new GatewayCreerRdvAction($c->get('guzzle.client.rdv'));
        },

        GatewayAnnulerRdvAction::class => function (ContainerInterface $c) {
            return new GatewayAnnulerRdvAction($c->get('guzzle.client.rdv'));
        },

        GatewaySignInAction::class => function (ContainerInterface $c) {
            return new GatewaySignInAction($c->get('guzzle.client.auth'));
        },

        GatewayRefreshAction::class => function (ContainerInterface $c) {
            return new GatewayRefreshAction($c->get('guzzle.client.auth'));
        },

        GatewayRegisterAction::class => function (ContainerInterface $c) {
            return new GatewayRegisterAction($c->get('guzzle.client.auth'));
        },

        GatewayValidateTokenAction::class => function (ContainerInterface $c) {
            return new GatewayValidateTokenAction($c->get('guzzle.client.auth'));
        },

        GatewayAuthnMiddleware::class => function (ContainerInterface $c) {
            return new GatewayAuthnMiddleware($c->get('guzzle.client.auth'));
        }
    ];
