<?php

namespace api_rdv\core\services\auth;

use api_rdv\application\providers\auth\JWTManager;
use api_rdv\core\dto\AuthDTO;
use api_rdv\core\dto\CredentialsDTO;
use api_rdv\core\repositoryInterfaces\AuthRepositoryInterface;
use api_rdv\core\services\auth\ServiceAuthInterface;
use api_rdv\application\exceptions\ServiceAuthInvalidDataException;

class ServiceAuth implements ServiceAuthInterface
{

    private AuthRepositoryInterface $authRepository;
    private JWTManager $jwtManager;

    public function __construct(AuthRepositoryInterface $authRepository, JWTManager $jwtManager)
    {
        $this->authRepository = $authRepository;
        $this->jwtManager = $jwtManager;
    }

    public function createUser(CredentialsDTO $credentials, int $role): string
    {
        $user = $this->authRepository->getUserByEmail($credentials->email);
        if (password_verify($credentials->password, $user->password)) {
            $payload = [
                'iat' => time(),
                'exp' => time() + 3600,
                'sub' => $user->id,
                'data' => [
                    'role' => $role,
                    'email' => $user->email
                ]
            ];

            return $this->jwtManager->createAccessToken($payload);
        }
        throw new ServiceAuthInvalidDataException("Invalid credentials");
    }

    public function byCredentials(CredentialsDTO $credentials): AuthDTO
    {
        $user = $this->authRepository->getUserByEmail($credentials->__get('email'));
        if (password_verify($credentials->__get('password'), $user->__get('password'))) {
            $payload = [
                'iat' => time(),
                'exp' => time() + 3600,
                'sub' => $user->id,
                'data' => [
                    'role' => $user->__get('role'),
                    'email' => $user->__get('email')
                ]
            ];

            return new AuthDTO($user->id, $user->email, $user->role, '', '');
        }
        throw new ServiceAuthInvalidDataException("Invalid credentials");
    }

    public function getUserById(string $id): AuthDTO
    {
        $user = $this->authRepository->getUserById($id);
        if ($user) {
            return new AuthDTO($user->id, $user->email, $user->role, '', '');
        }
        throw new ServiceAuthInvalidDataException("User not found");
    }
}
