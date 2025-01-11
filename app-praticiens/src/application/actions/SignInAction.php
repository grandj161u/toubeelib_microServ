<?php

namespace api_praticien\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use api_praticien\application\providers\auth\AuthProviderInterface;
use api_praticien\core\dto\CredentialsDTO;
use api_praticien\application\renderer\JsonRenderer;
use api_praticien\core\services\auth\ServiceAuthInvalidDataException;

class SignInAction extends AbstractAction
{

    protected AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $data = $rq->getParsedBody();
        $credentials = new CredentialsDTO($data['email'], $data['password']);

        try {
            $authDTO = $this->authProvider->signIn($credentials);
        } catch (ServiceAuthInvalidDataException $e) {
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
            return JsonRenderer::render($rs, 500, $data);
        }

        $data = [
            'token' => $authDTO->token,
            'role' => $authDTO->role,
            'links' => [
                'self' => ['href' => '/signin'],
                'refresh' => ['href' => '/refresh'],
                'signout' => ['href' => '/signout'],
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}
