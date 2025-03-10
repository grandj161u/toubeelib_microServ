<?php 

namespace api_auth\application\providers\auth;

use api_auth\core\dto\AuthDTO;
use api_auth\core\dto\CredentialsDTO;

interface AuthProviderInterface {
    public function register(CredentialsDTO $credentials, int $role): void;
    public function signin(CredentialsDTO $credentials): AuthDTO;
    public function refresh(string $token): AuthDTO;
    public function getSignedInUser(string $token): AuthDTO;
}