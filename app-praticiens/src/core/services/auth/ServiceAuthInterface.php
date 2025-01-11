<?php

namespace api_praticien\core\services\auth;

use api_praticien\core\dto\AuthDTO;
use api_praticien\core\dto\CredentialsDTO;

interface ServiceAuthInterface
{
    public function createUser(CredentialsDTO $credentials, int $role): string;
    public function byCredentials(CredentialsDTO $credentials): AuthDTO;
    public function getUserById(string $id): AuthDTO;
}
