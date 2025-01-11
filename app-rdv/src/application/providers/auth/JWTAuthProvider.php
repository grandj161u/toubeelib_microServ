<?php

namespace api_rdv\application\providers\auth;

use api_rdv\core\dto\AuthDTO;
use api_rdv\core\dto\CredentialsDTO;
use api_rdv\application\providers\auth\JWTManager;
use api_rdv\core\services\auth\ServiceAuthInterface;

class JWTAuthProvider implements AuthProviderInterface
{
    private ServiceAuthInterface $authService;
    private JWTManager $jwtManager;

    public function __construct(ServiceAuthInterface $authService, JWTManager $jwtManager)
    {
        $this->$authService = $authService;
        $this->jwtManager = $jwtManager;
    }

    public function register(CredentialsDTO $credentials, int $role): void
    {
        $this->authService->createUser($credentials, $role);
    }

    public function signin(CredentialsDTO $credentials): AuthDTO
    {
        $user = $this->authService->byCredentials($credentials);

        if (!$user) {
            throw new \Exception('Invalid credentials');
        }

        $accessToken = $this->jwtManager->createAccessToken(['id' => $user->ID]);
        $refreshToken = $this->jwtManager->createRefreshToken(['id' => $user->ID]);

        return new AuthDTO($user->ID, $user->email, $user->role, $accessToken, $refreshToken);
    }

    public function refresh(string $refreshToken): AuthDTO
    {
        $payload = $this->jwtManager->decodeToken($refreshToken);

        if (!$payload) {
            throw new \Exception('Invalid refresh token');
        }

        $newAccessToken = $this->jwtManager->createAccessToken($payload);
        $newRefreshToken = $this->jwtManager->createRefreshToken($payload);
        return new AuthDTO($payload['sub'], $payload['data']['email'], $payload['data']['role'], $newAccessToken, $newRefreshToken);
    }

    public function getSignedInUser(string $token): AuthDTO
    {
        $payload = $this->jwtManager->decodeToken($token);

        if (!$payload) {
            throw new \Exception('Invalid token');
        }

        $user = $this->authService->getUserById($payload['id']);

        if (!$user) {
            throw new \Exception('User not found');
        }

        return new AuthDTO($user->id, $user->email, $user->role, $token, $payload['exp']);
    }
}
