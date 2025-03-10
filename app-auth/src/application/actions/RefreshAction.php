<?php

namespace api_auth\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use api_auth\application\providers\auth\AuthProviderInterface;
use api_auth\application\renderer\JsonRenderer;

class RefreshAction extends AbstractAction {

    protected AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke (ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $data = $rq->getHeader('Authorization');
        $token = substr($data[0], 7);

        try {
            $authDTO = $this->authProvider->refresh($token);
        } catch (\Exception $e) {
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
            'token' => $authDTO->accessToken,
            'role' => $authDTO->role,
            'links' => [
                'self' => [ 'href' => '/users/refresh' ],
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}