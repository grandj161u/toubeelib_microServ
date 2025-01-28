<?php

namespace app_consumer\core\services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ServiceConsumer implements ServiceConsumerInterface
{
    private AMQPStreamConnection $connection;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    public function consumeMessage()
    {
        // $queue_name = "rdv.queue";
        // $channel = $this->connection->channel();

        // // $callback = function (AMQPMessage $msg) {
        // //     $msg_body = json_decode($msg->getBody(), true);
        // //     echo "[x] message reçu : \n";
        // //     $msg->getChannel()->basic_ack($msg->getDeliveryTag());
        // // };

        // // $channel->basic_consume(
        // //     $queue_name,
        // //     '',
        // //     false,
        // //     false,
        // //     false,
        // //     false,
        // //     $callback
        // // );

        // $msg = $channel->basic_get($queue_name, false);

        // echo "[x] message reçu : \n";
        // echo $msg->getBody();

        // // try {
        // //     $channel->consume();
        // // } catch (\Exception $e) {
        // //     echo $e->getMessage();
        // // }
        // $channel->close();
        // $this->connection->close();
    }
}
