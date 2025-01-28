<?php

namespace gateway_tblb\application\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpException;

class GatewayAuthnMiddleware
{
    private Client $client;

    public function __construct(Client $authRemote)
    {
        $this->client = $authRemote;
    }

    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        $route = $request->getUri()->getPath();
        $method = $request->getMethod();

        $publicRoutes = ['/users/signin', '/users/register'];
        $isPublicRoute = in_array($route, $publicRoutes) || $method === 'OPTIONS';

        if ($isPublicRoute) {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader) || !$this->isValidAuthHeader($authHeader[0])) {
            throw new HttpUnauthorizedException($request, "header invalide");
        }

        try {
            $response = $this->client->request('POST', '/tokens/validate', [
                'headers' => [
                    'Authorization' => $authHeader[0]
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new HttpUnauthorizedException($request, "Token invalide");
            }

            return $handler->handle($request);
        } catch (\GuzzleHttp\Exception\ClientException | \GuzzleHttp\Exception\ServerException $e) {
            $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            throw new HttpUnauthorizedException($request, $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function isValidAuthHeader(string $authHeader): bool
    {
        return str_starts_with($authHeader, 'Bearer ');
    }
}
