<?php

namespace gateway_tblb\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayListePraticienAction extends AbstractGatewayAction
{

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $response = $this->remote->request('GET', 'praticiens');
        $body = $response->getBody()->getContents();

        $rs->getBody()->write($body);
        return $rs->withHeader('Content-Type', 'application/json')
                  ->withStatus($response->getStatusCode());
    }
}
