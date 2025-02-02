<?php

namespace api_rdv\application\actions;

use api_rdv\core\services\rdv\ServiceRdvInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use api_rdv\application\renderer\JsonRenderer;
use api_rdv\core\services\rdv\ServiceRdvInternalErrorException;

class AnnulerRdvAction extends AbstractAction
{

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args["id"];

        try {
            $rdv_DTO = $this->serviceRdv->annulerRdv($id);
            $this->serviceRdv->sendMessageRdv("CANCEL", $id);
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
                'modifer' => ['href' => '/rdvs/' . $id],
                'annuler' => ['href' => '/rdvs/' . $id],
            ]
        ];

        return JsonRenderer::render($rs, 202, $data);
    }
}
