<?php

namespace api_praticien\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use api_praticien\core\services\praticien\ServicePraticienInterface;
use api_praticien\application\renderer\JsonRenderer;
use api_praticien\core\services\praticien\ServicePraticienNotFoundException;

class ListerPraticiensAction extends AbstractAction
{

    protected ServicePraticienInterface $servicePrat;

    public function __construct(ServicePraticienInterface $servicePrat)
    {
        $this->servicePrat = $servicePrat;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        try {

            $prat_DTO = $this->servicePrat->getAllPraticien();
        } catch (ServicePraticienNotFoundException $e) {
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
            'praticiens' => $prat_DTO,
            'links' => [
                'self' => ['href' => '/praticiens'],
            ]
        ];


        return JsonRenderer::render($rs, 200, $data);
    }
}
