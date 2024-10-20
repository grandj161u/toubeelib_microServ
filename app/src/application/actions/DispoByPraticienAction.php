<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRdvNotFoundException;

class DispoByPraticienAction extends AbstractAction {

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke (ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $id = $args["idPraticien"];

        try {
            $dateDebut = new \DateTimeImmutable($args["dateDebut"]);
            $dateFin = new \DateTimeImmutable($args["dateFin"]);
            $tabDispo = $this->serviceRdv->getDisponibiliterPraticien($id, $dateDebut, $dateFin);
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
            'disponibilités' => $tabDispo,
            'links' => [
                'self' => [ 'href' => '/DispoPraticien/' . $id . '/' . $args["dateDebut"] . '/' . $args["dateFin"] ],
                'planning' => [ 'href' => '/PlanningPraticien/' . $id . '/' . $args["dateDebut"] . '/' . $args["dateFin"] ]
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);

    }
}