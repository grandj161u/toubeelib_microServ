<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRdvNotFoundException;

class PlanningPraticienAction extends AbstractAction
{

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $id = $args["idPraticien"];
        $dateDebutParam = $rq->getQueryParams()["debut"] ?? null;
        $dateFinParam = $rq->getQueryParams()["fin"] ?? new \DateTimeImmutable('now');

        try {
            $dateDebut = $dateDebutParam ? new \DateTimeImmutable($dateDebutParam) : $dateFinParam->sub(new \DateInterval('P7D'));
            $dateFin = $dateFinParam instanceof \DateTimeImmutable ? $dateFinParam : new \DateTimeImmutable($dateFinParam);
            $tabDispo = $this->serviceRdv->getPlanningPraticien($id, $dateDebut, $dateFin);
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
            'planning' => $tabDispo,
            'links' => [
                'self' => ['href' => '/praticens/' . $id . '/rdvs?debut=' . $dateDebut->format('Y-m-d') . '&fin=' . $dateFin->format('Y-m-d')],
                'disponibilités' => ['href' => '/praticiens/' . $id . '/dispos?debut=' . $dateDebut->format('Y-m-d') . '&fin=' . $dateFin->format('Y-m-d')]
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}
