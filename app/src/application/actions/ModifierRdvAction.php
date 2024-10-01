<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRdvNotFoundException;

class ModifierRdvAction extends AbstractAction {

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke (ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args["id"];

        try {
            $idSpecialite = $rq->getParsedBody()["idSpecialite"] ?? null;
            $idPatient = $rq->getParsedBody()["idPatient"] ?? null;
            
            $rdv_DTO = $this->serviceRdv->modifierRdv($id, $idSpecialite, $idPatient);
        } catch (ServiceRdvNotFoundException $e) {
           $data = [
                'message' => $e->getMessage(),
                'exception' => [
                    'type' => get_class($e),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
            $rdv_DTO = $this->serviceRdv->modifierRdv($id, $idSpecialite, $idPatient);
        } catch (\Exception $e) {
            $rs->getBody()->write($e->getMessage());
            return $rs->withStatus(404);
        } catch (\Exception  $e) {
            $data = [
                'message' => $e->getMessage(),
                'exception' => [
                    'type' => get_class($e),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
            return $rs->withStatus(400);
        }

        $data = [
            'rdv' => $rdv_DTO,
            'links' => [
                'self' => [ 'href' => '/Rdvs/' . $id ], 
                'modifier' => [ 'href' => '/Rdvs/' . $id ]
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}