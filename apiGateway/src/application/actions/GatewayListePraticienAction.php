<?php

namespace gateway_tblb\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;

class GatewayListePraticienAction extends AbstractGatewayAction
{

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        try {
            $response = $this->remote->request('GET', 'praticiens');
           
        } catch (ConnectException | ServerException $e) {
            throw new HttpInternalServerErrorException($rq, "Internal server error");
        } catch (ClientException $e ) {
            match($e->getCode()) {
                404 => throw new HttpNotFoundException($rq, "Ressource not found"),
                403 => throw new HttpForbiddenException($rq, "Access forbidden"),
                400 => throw new HttpBadRequestException($rq, "Bad request")
            };
        }
        return $response;
    }
}
