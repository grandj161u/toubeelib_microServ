<?php 

namespace api_praticien\application\middlewares;

use api_praticien\core\services\praticien\authorization\AuthzPraticienServiceInterface;
use api_praticien\core\services\auth\ServiceAuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;
use Ramsey\Uuid\Uuid;

class AuthzPraticienMiddleware
{
    protected AuthzPraticienServiceInterface $authzPraticienService;
    protected ServiceAuthInterface $serviceAuth;

    public function __construct(AuthzPraticienServiceInterface $authzPraticienService, ServiceAuthInterface $serviceAuth)
    {
        $this->authzPraticienService = $authzPraticienService;
        $this->serviceAuth = $serviceAuth;
    }

    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        $authHeader = $request->getHeader('Authorization');
        $token = substr($authHeader[0], 7);
        
        if (!$token) {
            throw new HttpForbiddenException($request, "Token manquant");
        }

        $decodedToken = $this->serviceAuth->decodeToken($token);

        $userId = Uuid::fromString($decodedToken->ID);
        $role = $decodedToken->role;

        $operation = $this->getOperationFromMethod($request->getMethod());

        $route = $request->getAttribute('__route__');
        $resourceId = null;
        if ($route !== null) {
            $routeArgs = $route->getArguments();
            if (isset($routeArgs['idPraticien'])) {
                try {
                $resourceId = Uuid::fromString($routeArgs['idPraticien']);
                } catch (\Exception $e) {
                    throw $e;
                }
            } else if(isset($routeArgs['idSpecialite'])) {
                $resourceId = Uuid::fromString($routeArgs['idSpecialite']);
            }
        }

        if (!$this->authzPraticienService->isGranted($userId, $role, $operation, $resourceId)) {
            throw new HttpForbiddenException($request, "Accès refusé");
        }
        
        return $handler->handle($request);   
    }

    private function getOperationFromMethod(string $method): int
    {
        return match ($method) {
            'GET' => 1,
            'POST' => 2,
            'PUT' => 3,
            'DELETE' => 4,
            default => throw new \InvalidArgumentException("Méthode HTTP non supportée")
        };
    }
}