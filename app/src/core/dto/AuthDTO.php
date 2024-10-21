<?php 

namespace toubeelib\core\dto;

class AuthDTO extends DTO {
    
    protected string $ID;
    protected string $email;
    protected int $role;
    protected string $accessToken;
    protected string $refreshToken;

    public function __construct($ID, $email, $role, $accessToken, $refreshToken) {
        $this->ID = $ID;
        $this->email = $email;
        $this->role = $role;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    public function jsonSerialize(): array{
        return [
            "ID" => $this->ID,
            "email" => $this->email,
            "role" => $this->role,
            "accessToken" => $this->accessToken,
            "refreshToken" => $this->refreshToken
        ];
    }
}