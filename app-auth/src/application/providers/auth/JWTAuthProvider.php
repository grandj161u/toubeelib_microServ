<?php

namespace api_auth\application\providers\auth;

use api_auth\core\dto\AuthDTO;
use api_auth\core\dto\CredentialsDTO;
use api_auth\application\providers\auth\JWTManager;
use api_auth\core\services\auth\ServiceAuthInterface;

class JWTAuthProvider implements AuthProviderInterface {
    private ServiceAuthInterface $authService;
    private JWTManager $jwtManager;

    public function __construct(ServiceAuthInterface $authService, JWTManager $jwtManager) {
        $this->authService = $authService;
        $this->jwtManager = $jwtManager;
    }

    public function register(CredentialsDTO $credentials, int $role): void {
        $this->authService->createUser($credentials, $role);
    }

    public function signin(CredentialsDTO $credentials): AuthDTO {
        $user = $this->authService->byCredentials($credentials);
        
        if (!$user) {
            throw new \Exception('Invalid credentials');
        }

        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $user->ID,
            'data' => [
                'user' => $user->email,
                'role' => $user->role
            ]
        ];

        $accessToken = $this->jwtManager->createAccessToken($payload);
        $refreshToken = $this->jwtManager->createRefreshToken($payload);

        return new AuthDTO($user->ID, $user->email, $user->role, $accessToken, $refreshToken);
    }

    public function refresh(string $refreshToken): AuthDTO {
        $decodedToken = $this->jwtManager->decodeToken($refreshToken);
        $userId = $decodedToken['sub'];

        $user = $this->authService->getUserById($userId); 

        if (!$user) {
            throw new \Exception('Invalid refresh token');
        }

        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $user->ID,
            'data' => [
                'user' => $user->email,
                'role' => $user->role
            ]
        ];

        $newAccessToken = $this->jwtManager->createAccessToken($payload);
        $newRefreshToken = $this->jwtManager->createRefreshToken($payload);

        return new AuthDTO($user->ID, $user->email, $user->role, $newAccessToken, $newRefreshToken);
    }

    public function getSignedInUser(string $token): AuthDTO {
        $decodedToken = $this->jwtManager->decodeToken($token);

        if (!$decodedToken) {
            throw new \Exception('Invalid token');
        }

        $user = $this->authService->getUserById($decodedToken['sub']);
        
        $refreshToken = $this->jwtManager->createRefreshToken($decodedToken);

        if (!$user) {
            throw new \Exception('User not found');
        }

        return new AuthDTO($user->ID, $user->email, $user->role, $token, $refreshToken);
    }
}
