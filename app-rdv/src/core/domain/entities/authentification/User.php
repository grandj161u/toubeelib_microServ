<?php

namespace api_rdv\core\domain\entities\authentification;

use api_rdv\core\domain\entities\Entity;

class User extends Entity
{

    protected string $email;
    protected string $password;
    protected int $role;

    public function __construct(string $email, string $password, int $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }
}
