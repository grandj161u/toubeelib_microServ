<?php

use app_consumer\application\actions\ConsumeMessageAction;
use app_consumer\application\actions\HomeAction;
use app_consumer\core\services\ServiceConsumer;
use app_consumer\core\services\ServiceConsumerInterface;
use Psr\Container\ContainerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

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

        'rabbitmq.connection.rdv' => function (ContainerInterface $c) {
            $connection = new AMQPStreamConnection(
                $c->get('settings')['rabbitmq.host'],
                $c->get('settings')['rabbitmq.port'],
                $c->get('settings')['rabbitmq.user'],
                $c->get('settings')['rabbitmq.password']
            );
            return $connection;
        },

        ServiceConsumerInterface::class => function (ContainerInterface $c) {
            return new ServiceConsumer($c->get('rabbitmq.connection.rdv'));
        },

        ConsumeMessageAction::class => function (ContainerInterface $c) {
            return new ConsumeMessageAction($c->get(ServiceConsumerInterface::class));
        },
    ];
