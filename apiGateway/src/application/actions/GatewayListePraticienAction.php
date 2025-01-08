<?php

namespace gateway_tblb\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayListePraticienAction extends AbstractGatewayAction
{

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args)
    {
        $response = $this->remote->request('GET', 'Praticiens');
        $data = json_decode((string) $response->getBody(), true);
        echo $data;
    }
}
