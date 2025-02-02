<?php

namespace app_consumer\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeAction extends AbstractConsumerAction
{


    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $rs->getBody()->write('Hello World');
        return $rs;
    }
}
