<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\application\renderer\JsonRenderer;

class ModifierRdvAction extends AbstractAction {

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke (ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args["id"];

        try {
            $specialite = $rq->getParsedBody()["specialite"] ?? null;
            $idPatient = $rq->getParsedBody()["idPatient"] ?? null;
            
            $rdv_DTO = $this->serviceRdv->modifierRdv($id, $specialite, $idPatient);
        } catch (\Exception $e) {
            $rs->getBody()->write($e->getMessage());
            return $rs->withStatus(404);
        }

        $data = [
            'rdv' => $rdv_DTO
        ];

        return JsonRenderer::render($rs, 200, $data);

    }
}