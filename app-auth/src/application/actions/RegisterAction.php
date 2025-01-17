<?php 

namespace api_auth\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use api_auth\application\providers\auth\AuthProviderInterface;
use api_auth\core\dto\CredentialsDTO;
use api_auth\application\renderer\JsonRenderer;
use api_auth\core\services\exceptions\ServiceAuthInvalidDataException;

class RegisterAction extends AbstractAction 
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
            $this->authProvider->register($credentials,0);
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
            'message' => 'User registered successfully',
            'links' => [
                'self' => [ 'href' => '/users/register' ],
                'refresh' => [ 'href' => '/users/refresh' ],
                'signin' => [ 'href' => '/users/signin' ],
            ]
        ];

        return JsonRenderer::render($rs, 200, $data);
    }
}