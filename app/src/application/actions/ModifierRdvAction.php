<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRdvNotFoundException;
use toubeelib\core\dto\ModifyRdvDTO;

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

            $modify_RdvDTO = new ModifyRdvDTO($idPatient, $idSpecialite);
            
            print_r($modify_RdvDTO);


            $rdv_DTO = $this->serviceRdv->modifierRdv($modify_RdvDTO, $id);
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
            return JsonRenderer::render($rs, 404, $data);
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
            return JsonRenderer::render($rs, 400, $data);
        }

        $data = [
            'rdv' => $rdv_DTO,
            'links' => [
                'self' => [ 'href' => '/Rdvs/' . $id ], 
                'modifier' => [ 'href' => '/Rdvs/' . $id ],
                'annuler' => [ 'href' => '/Rdvs/' . $id ],
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}