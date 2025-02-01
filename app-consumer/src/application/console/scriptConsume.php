<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use app_consumer\core\services\mails\ServiceMailInterface;

$container = require __DIR__ . '/../../../config/bootstrap.php';
$serviceMail = $container->get(ServiceMailInterface::class);

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'root');
$channel = $connection->channel();

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function ($msg) use ($serviceMail) {
    $messageData = json_decode($msg->getBody(), true);
    echo " [x] Received ", $msg->getBody(), "\n";

    try {
        if ($messageData['message'] === "CREATE") {
            $serviceMail->notifyRdvCreated($messageData);
        } elseif ($messageData['message'] === "CANCEL") {
            $serviceMail->notifyRdvCanceled($messageData);
        }

        echo " [x] Email sent for message type {$messageData['message']}\n";
    } catch (\Exception $e) {
        echo " [x] Error sending email: {$e->getMessage()}\n";
    }
};

$channel->basic_consume('rdv.queue', '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Exception $e) {
    echo $e->getMessage();
}

$channel->close();
$connection->close();
