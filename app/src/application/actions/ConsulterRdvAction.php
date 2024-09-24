<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;

class ConsulterRdvAction extends AbstractAction {

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke (ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args["id"];

        try {
            $rdv = $this->serviceRdv->getRdvById($id);
            $rs->getBody()->write(json_encode($rdv));
            return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $rs->getBody()->write($e->getMessage());
            return $rs->withStatus(404);
        }

    }
}