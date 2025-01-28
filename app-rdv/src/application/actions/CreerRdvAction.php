<?php

namespace api_rdv\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use api_rdv\core\services\rdv\ServiceRdvInterface;
use api_rdv\application\renderer\JsonRenderer;
use api_rdv\core\dto\InputRdvDTO;
use api_rdv\core\services\rdv\ServiceRdvInternalErrorException;

class CreerRdvAction extends AbstractAction
{

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {

        try {
            $idPraticien = $rq->getParsedBody()["idPraticien"] ?? null;
            $idPatient = $rq->getParsedBody()["idPatient"] ?? null;
            $horaireData = $rq->getParsedBody()["horaire"] ?? null;
            $idSpecialite = $rq->getParsedBody()["idSpecialite"] ?? null;
            $type = $rq->getParsedBody()["type"] ?? null;
            $statut = $rq->getParsedBody()["statut"] ?? null;

            $horaire = new \DateTimeImmutable($horaireData['date'], new \DateTimeZone($horaireData['timezone']));

            $inputRdvDTO = new InputRdvDTO($idPraticien, $idPatient, $horaire, $idSpecialite, $type, $statut);
            $rdv_DTO = $this->serviceRdv->creerRdv($inputRdvDTO);

            $this->serviceRdv->sendMessageRdv("CREATE", $rdv_DTO->ID);
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
            'rdv' => $rdv_DTO
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}
