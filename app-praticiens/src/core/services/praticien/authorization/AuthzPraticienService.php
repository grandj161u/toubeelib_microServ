<?php

namespace api_praticien\core\services\praticien\authorization;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AuthzPraticienService implements AuthzPraticienServiceInterface
{

    private const ADMIN = 10;
    private const PRATICIEN = 5;
    private const USER = 0;
    private const OP_READ = 1;
    private const OP_CREATE = 2;
    private const OP_UPDATE = 3;
    private const OP_DELETE = 4;

    /**
     * Vérifie si l'utilisateur a les droits d'accès à la ressource
     * @param \Faker\Core\Uuid $user_id
     * @param int $role
     * @param int $operation
     * @param \Faker\Core\Uuid $resource_id
     * @return bool
     */
    public function isGranted(UuidInterface $user_id, int $role, int $operation, UuidInterface $resource_id): bool
    {
        if ($role == self::ADMIN) {
            return true;
        }
        
        if ($role == self::PRATICIEN) {
            if (($operation == self::OP_UPDATE || $operation == self::OP_READ) && $user_id == $resource_id) {
                return true;
            }
        }
        
        if ($role == self::USER) {
            return false;
        }

        return false;
    }
}