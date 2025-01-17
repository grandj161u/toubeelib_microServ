<?php

namespace api_auth\core\services\auth;

use api_auth\core\dto\AuthDTO;
use api_auth\core\dto\CredentialsDTO;

interface ServiceAuthInterface {
    public function createUser(CredentialsDTO $credentials, int $role): string;
    public function byCredentials(CredentialsDTO $credentials): AuthDTO;
    public function getUserById(string $id): AuthDTO;
}