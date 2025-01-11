<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRdvInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRdvNotFoundException;

class DispoByPraticienAction extends AbstractAction
{

    protected ServiceRdvInterface $serviceRdv;

    public function __construct(ServiceRdvInterface $serviceRdv)
    {
        $this->serviceRdv = $serviceRdv;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {

        $id = $args["idPraticien"];
        $aujourdhui = new \DateTimeImmutable('now');

        // Si l'heure d'aujourd'hui est supérieure ou égale à 18h, on prend la date de demain à 8h (inutile de chercher des dispos pour aujourd'hui)
        if ($aujourdhui->format('H') >= 18) {
            $dateDebutParam = $rq->getQueryParams()["debut"] ?? (new \DateTimeImmutable('tomorrow'))->setTime(8, 0, 0);
        } else {
            $dateDebutParam = $rq->getQueryParams()["debut"] ?? (new \DateTimeImmutable('now'))->setTime(8, 0, 0);
        }

        $dateFinParam = $rq->getQueryParams()["fin"] ?? null;

        try {
            $dateDebut = $dateDebutParam instanceof \DateTimeImmutable ? $dateDebutParam : new \DateTimeImmutable($dateDebutParam);
            $dateFin = $dateFinParam ? new \DateTimeImmutable($dateFinParam) : $dateDebut->add(new \DateInterval('P7D'));
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
                'self' => ['href' => '/praticiens/' . $id . '/dispos?debut=' . $dateDebut->format('Y-m-d') . '&fin=' . $dateFin->format('Y-m-d')],
                'planning' => ['href' => '/praticiens/' . $id . '/rdvs?debut=' . $dateDebut->format('Y-m-d') . '&fin=' . $dateFin->format('Y-m-d')]
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}
