<?php

namespace api_rdv\core\services\auth;

use api_rdv\core\dto\AuthDTO;
use api_rdv\core\dto\CredentialsDTO;

interface ServiceAuthInterface
{
    public function createUser(CredentialsDTO $credentials, int $role): string;
    public function byCredentials(CredentialsDTO $credentials): AuthDTO;
    public function getUserById(string $id): AuthDTO;
}
