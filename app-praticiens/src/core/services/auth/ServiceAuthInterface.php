<?php

namespace api_praticien\core\services\auth;

use api_praticien\core\dto\AuthDTO;

interface ServiceAuthInterface {
    public function decodeToken(string $token): AuthDTO;
}