<?php

namespace api_rdv\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use api_rdv\core\services\rdv\ServiceRdvInterface;
use api_rdv\application\renderer\JsonRenderer;
use api_rdv\core\services\rdv\ServiceRdvNotFoundException;

class PlanningOuDispoPraticienAction extends AbstractAction
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
        $statut = $rq->getQueryParams()["statut"] ?? 'planning';
        $dateDebutParam = $rq->getQueryParams()["debut"] ?? new \DateTimeImmutable('now');
        $dateFinParam = $rq->getQueryParams()["fin"] ?? null;

        if ($statut == 'planning' || $statut == 'dispo') {

            // Si l'heure d'aujourd'hui est supérieure ou égale à 18h, on prend la date de demain à 8h (inutile de chercher des rdvs pour aujourd'hui)
            if ($aujourdhui->format('H') >= 18) {
                $dateDebutParam = $rq->getQueryParams()["debut"] ?? (new \DateTimeImmutable('tomorrow'))->setTime(8, 0, 0);
            } else {
                $dateDebutParam = $rq->getQueryParams()["debut"] ?? (new \DateTimeImmutable('now'))->setTime(8, 0, 0);
            }
        } else {
            throw new \Exception("Le paramètre statut doit être égal à 'planning' ou 'dispo' ou non spécifier", 400);
        }

        try {
            $dateDebut = $dateDebutParam instanceof \DateTimeImmutable ? $dateDebutParam : new \DateTimeImmutable($dateDebutParam);
            $dateFin = $dateFinParam ? new \DateTimeImmutable($dateFinParam) : $dateDebut->add(new \DateInterval('P7D'));

            if ($statut == 'planning') {
                $tabDispo = $this->serviceRdv->getPlanningPraticien($id, $dateDebut, $dateFin);
            } elseif ($statut == 'dispo') {
                $tabDispo = $this->serviceRdv->getDisponibiliterPraticien($id, $dateDebut, $dateFin);
            }
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
                'self' => ['href' => '/praticens/' . $id . '/rdvs?statut=' . $statut . '&debut=' . $dateDebut->format('Y-m-d') . '&fin=' . $dateFin->format('Y-m-d')],
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}
