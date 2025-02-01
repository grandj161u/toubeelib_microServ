<?php

namespace gateway_tblb\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Exception\RequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;

class GatewayGenericAction extends AbstractGatewayAction
{
    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $uri = $rq->getUri()->getPath();
        $method = $rq->getMethod();
        $options = [
            'headers' => $rq->getHeaders(),
            'query' => $rq->getQueryParams(),
            'body' => $rq->getBody()->getContents()
        ];

        try {
            $response = $this->remote->request($method, $uri, $options);
        } catch (RequestException $e) {
            match ($e->getCode()) {
                404 => throw new HttpNotFoundException($rq, "Not found: " . $e->getMessage()),
                403 => throw new HttpForbiddenException($rq, "Access forbidden: " . $e->getMessage()),
                400 => throw new HttpBadRequestException($rq, "Bad request: " . $e->getMessage()),
                default => throw new HttpInternalServerErrorException($rq, "Internal server error: " . $e->getMessage()),
            };
        }
        return $response;
    }
}