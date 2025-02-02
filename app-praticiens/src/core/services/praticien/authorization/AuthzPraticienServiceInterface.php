<?php 

namespace api_praticien\core\services\praticien\authorization;

use Ramsey\Uuid\UuidInterface;

interface AuthzPraticienServiceInterface
{
    public function isGranted(UuidInterface $user_id, int $role, int $operation, UuidInterface $resource_id): bool;
}