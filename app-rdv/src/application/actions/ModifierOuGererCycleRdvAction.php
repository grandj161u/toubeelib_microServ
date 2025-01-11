<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\dto\ModifyRdvDTO;
use toubeelib\core\dto\GererCycleRdvDTO;
use toubeelib\core\services\rdv\ServiceRdvInternalErrorException;

class ModifierOuGererCycleRdvAction extends AbstractAction
{

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args["id"];
        $body = $rq->getParsedBody();

        try {
            if (isset($body['statut'])) {
                $statut = $body["statut"];

                $gererCycleRdv_DTO = new GererCycleRdvDTO($statut);

                $rdv_DTO = $this->serviceRdv->GererCycleRdv($gererCycleRdv_DTO, $id);
            }

            if (isset($body['idSpecialite']) || isset($body['idPatient'])) {
                $idSpecialite = $body["idSpecialite"] ?? null;
                $idPatient = $body["idPatient"] ?? null;

                $modify_RdvDTO = new ModifyRdvDTO($idPatient, $idSpecialite);

                $rdv_DTO = $this->serviceRdv->modifierRdv($modify_RdvDTO, $id);
            }
        } catch (ServiceRdvInternalErrorException $e) {
            $data = [
                'message' => $e->getMessage(),
                'exception' => [
                    'type' => get_class($e),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
            return JsonRenderer::render($rs, 500, $data);
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
                'self' => ['href' => '/rdvs/' . $id],
                'modifier' => ['href' => '/rdvs/' . $id],
                'annuler' => ['href' => '/rdvs/' . $id],
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}
