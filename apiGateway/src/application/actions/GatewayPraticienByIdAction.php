<?php

namespace gateway_tblb\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayPraticienByIdAction extends AbstractGatewayAction
{

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        if (isset($args['idPraticien'])) {
            $idPraticien = $args['idPraticien'];
        } else {
            $idPraticien = null;
        }

        $response = $this->remote->request('GET', 'praticiens/' . $idPraticien);
        // $body = $response->getBody()->getContents();

        // $rs->getBody()->write($body);
        // return $rs->withHeader('Content-Type', 'application/json')
        //     ->withStatus($response->getStatusCode());
        return $response;
    }
}
