<?php

namespace api_auth\application\actions;

use api_praticien\application\providers\auth\AuthProviderInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use api_auth\application\actions\AbstractAction;
use api_auth\application\renderer\JsonRenderer;
use api_auth\core\services\exceptions\ServiceAuthInvalidDataException;

class ValidateTokenAction extends AbstractAction
{

    protected AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(RequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $token = $rq->getHeader('Authorization')[0];

        if(!$token) {
            $data = [
                'message' => 'Token not found in request header',
                'exception' => [
                    'type' => 'TokenNotFoundException',
                    'code' => 404,
                    'file' => __FILE__,
                    'line' => __LINE__
                ]
            ];
            return JsonRenderer::render($rs, 404, $data);
        }

        try {
            $authDTO = $this->authProvider->getSignedInUser($token);
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
            'message' => 'Token is valid',
            'token' => $authDTO->accessToken,
            'links' => [
                'self' => [ 'href' => '/tokens/validate' ],
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}