<?php

namespace toubeelib\application\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use toubeelib\application\providers\auth\AuthProviderInterface;

class AuthMiddleware
{
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, RequestHandler $handler) 
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader) || !$this->isValidAuthHeader($authHeader)) {
            return new Response(401);
        }

        $token = $this->extractToken($authHeader);

        try {
            $authDTO = $this->authProvider->getSignedInUser($token);

            $request = $request->withAttribute('email', $authDTO->email);
            $request = $request->withAttribute('role', $authDTO->role);

            return $handler->handle($request);
        } catch (\Exception $e) {
            return new Response(401);
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
