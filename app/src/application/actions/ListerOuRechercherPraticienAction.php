<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\application\renderer\JsonRenderer;
use toubeelib\core\services\rdv\ServiceRdvNotFoundException;

class ListerOuRechercherPraticienAction extends AbstractAction {

    protected ServicePraticienInterface $servicePrat;

    public function __construct(ServicePraticienInterface $servicePrat) {
        $this->servicePrat = $servicePrat;
    }

    public function __invoke (ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {

        // Récupérer les paramètres de l'URL
        $queryParams = $rq->getQueryParams();
    
        // Récupérer un paramètre spécifique (idPraticien)
        $idPraticien = $queryParams['idPraticien'] ?? null;

        try {
        if($idPraticien){
            $prat_DTO = $this->servicePrat->getPraticienById($idPraticien);
        } else{
            $prat_DTO = $this->servicePrat->getAllPraticien();
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

         if ($idPraticien) {
            $data = [
                'praticien' => $prat_DTO,
                'links' => [
                    'self' => [ 'href' => '/Praticiens?idPraticien=' . $idPraticien ],
                ]
            ];
         } else {
            $data = [
                'praticiens' => $prat_DTO,
                'links' => [
                    'self' => [ 'href' => '/Praticiens'],
                ]
            ];
         }

        return JsonRenderer::render($rs, 200, $data);

    }
}