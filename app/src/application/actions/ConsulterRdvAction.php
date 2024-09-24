<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\application\renderer\JsonRenderer;

class ConsulterRdvAction extends AbstractAction {

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke (ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args["id"];

        try {
            $rdv = $this->serviceRdv->getRdvById($id);

            $data = [
                "id" => $rdv->ID,
                "idPraticien" => $rdv->idPraticien,
                "idPatient" => $rdv->idPatient,
                "date" => $rdv->horaire,
                "idSpecialite" => $rdv->idSpecialite,
                "type" => $rdv->type,
                "statut" => $rdv->statut
            ];

            return JsonRenderer::render($rs, 200, $data);
        } catch (\Exception $e) {
            $rs->getBody()->write($e->getMessage());
            return $rs->withStatus(404);
        }

    }
}