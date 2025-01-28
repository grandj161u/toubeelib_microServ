<?php

namespace app_consumer\application\actions;

use app_consumer\core\services\ServiceConsumerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ConsumeMessageAction extends AbstractConsumerAction
{

    private ServiceConsumerInterface $serviceConsumer;

    public function __construct(ServiceConsumerInterface $serviceConsumer)
    {
        $this->serviceConsumer = $serviceConsumer;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {

        $message = $this->serviceConsumer->consumeMessage();


        $rs->getBody()->write($message);
        return $rs;
    }
}
