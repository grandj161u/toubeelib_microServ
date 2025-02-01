<?php

use Psr\Container\ContainerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use app_consumer\core\services\mails\ServiceMail;
use app_consumer\core\services\mails\ServiceMailInterface;

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

        'mailer.config' => function (ContainerInterface $c) {
            return parse_ini_file(__DIR__ . '/mailer.ini', true)['mailer'];
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

        ServiceMailInterface::class => function (ContainerInterface $c) {
            $config = $c->get('mailer.config');
            $transport = Transport::fromDsn($config['dsn']);
            $mailer = new Mailer($transport);
            return new ServiceMail($mailer, $config['from']);
        },
    ];
