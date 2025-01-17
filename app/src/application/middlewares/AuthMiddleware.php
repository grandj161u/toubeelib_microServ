<?php

namespace toubeelib\application\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use toubeelib\application\providers\auth\AuthProviderInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpException;
use toubeelib\core\dto\AuthDTO;

class AuthMiddleware
{
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, RequestHandler $handler) 
    {
        $route = $request->getUri()->getPath();
        $method = $request->getMethod();

        $publicRoutes = ['/users/signin', '/register'];
        $isPublicRoute = in_array($route, $publicRoutes) || $method === 'OPTIONS';

        if ($isPublicRoute) {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader) || !$this->isValidAuthHeader($authHeader[0])) {
            return new HttpUnauthorizedException($request, "header invalide");
        }

        $token = $this->extractToken($authHeader[0]);

        try {
            $authDTO = $this->authProvider->getSignedInUser($token);
            
            $request = $request->withAttribute('email', $authDTO->email);
            $request = $request->withAttribute('role', $authDTO->role);

            return $handler->handle($request);
        } catch (\Exception $e) {
            return new HttpException($request, $e->getMessage() ,401);
        }
    }
    
    private function isValidAuthHeader(string $authHeader): bool
    {
        return str_starts_with($authHeader, 'Bearer ');
    }

    private function extractToken(string $authHeader): string
    {
        return substr($authHeader, 7);
    }
}
