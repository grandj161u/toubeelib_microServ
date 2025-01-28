<?php

namespace app_consumer\application\console;

require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'root');
$channel = $connection->channel();

// $channel->queue_declare('rdv.queue', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function ($msg) {
    echo " [x] Received ", $msg->getBody(), "\n";
};

$channel->basic_consume('rdv.queue', '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Exception $e) {
    echo $e->getMessage();
}

$channel->close();
$connection->close();
