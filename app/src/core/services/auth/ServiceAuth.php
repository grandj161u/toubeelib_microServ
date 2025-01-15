<?php

namespace toubeelib\core\services\auth;

use toubeelib\application\providers\auth\JWTManager;
use toubeelib\core\dto\AuthDTO;
use toubeelib\core\dto\CredentialsDTO;
use toubeelib\core\repositoryInterfaces\AuthRepositoryInterface;
use toubeelib\core\services\auth\ServiceAuthInterface;
use toubeelib\core\services\exceptions\ServiceAuthInvalidDataException;

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
        $user = $this->authRepository->creerUser($credentials->email, $credentials->password, $role);
        return $user->getID();
    }

    public function byCredentials(CredentialsDTO $credentials): AuthDTO
    {
        $user = $this->authRepository->getUserByEmail($credentials->__get('email'));
        if (password_verify($credentials->__get('password'), $user->__get('password'))) {
            $payload = [
                'iat' => time(),
                'exp' => time() + 3600,
                'sub' => $user->getID(),
                'data' => [
                    'role' => $user->role,
                    'email' => $user->email
                ]
            ];

            return new AuthDTO($user->getID(), $user->email, $user->role, '', '');
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
