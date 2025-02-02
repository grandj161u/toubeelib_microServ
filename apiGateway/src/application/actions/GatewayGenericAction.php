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
            $errorResponse = $e->getResponse();
            $errorBody = json_decode($errorResponse?->getBody()->getContents(), true);

            $errorData = [
                'message' => match ($e->getCode()) {
                    404 => "Not found",
                    403 => "Access forbidden",
                    400 => "Bad request",
                    500 => "Internal server error",
                    default => "Error"
                },
                'details' => $errorBody ?? $e->getMessage(),
                'status' => $e->getCode()
            ];

            throw match ($e->getCode()) {
                404 => new HttpNotFoundException($rq, json_encode($errorData)),
                403 => new HttpForbiddenException($rq, json_encode($errorData)),
                400 => new HttpBadRequestException($rq, json_encode($errorData)),
                default => new HttpInternalServerErrorException($rq, json_encode($errorData)),
            };
        }
        return $response;
    }
}