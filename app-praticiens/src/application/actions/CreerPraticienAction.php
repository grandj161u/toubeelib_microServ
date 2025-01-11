<?php

namespace api_praticien\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use api_praticien\core\services\praticien\ServicePraticienInterface;
use api_praticien\application\renderer\JsonRenderer;
use api_praticien\core\dto\InputPraticienDTO;
use api_praticien\core\services\praticien\ServicePraticienInternalErrorException;

class CreerPraticienAction extends AbstractAction
{

    protected ServicePraticienInterface $servicePraticien;

    public function __construct(ServicePraticienInterface $servicePraticien)
    {
        $this->servicePraticien = $servicePraticien;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {

        try {
            $nom = $rq->getParsedBody()["nom"] ?? null;
            $prenom = $rq->getParsedBody()["prenom"] ?? null;
            $tel = $rq->getParsedBody()["tel"] ?? null;
            $adresse = $rq->getParsedBody()["adresse"] ?? null;
            $specialite_id = $rq->getParsedBody()["specialite_id"] ?? null;

            $inputPraticienDTO = new InputPraticienDTO($nom, $prenom, $tel, $adresse, $specialite_id);
            $praticien_DTO = $this->servicePraticien->createPraticien($inputPraticienDTO);
        } catch (ServicePraticienInternalErrorException $e) {
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
            'praticien' => $praticien_DTO
        ];


        return JsonRenderer::render($rs, 200, $data);
    }
}
