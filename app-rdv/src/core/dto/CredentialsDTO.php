<?php

namespace api_rdv\core\dto;

class CredentialsDTO extends DTO
{

    protected string $email;
    protected string $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function jsonSerialize(): array
    {
        return [
            "email" => $this->email,
            "password" => $this->password
        ];
    }
}
