<?php

namespace gateway_tblb\application\actions;

use GuzzleHttp\ClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractGatewayAction
{

    public ClientInterface $remote;

    function __construct(ClientInterface $client)
    {
        $this->remote = $client;
    }

    abstract public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface;
}
