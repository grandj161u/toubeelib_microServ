<?php 

namespace api_auth\core\repositoryInterfaces;

use api_auth\core\domain\entities\authentification\User;

interface AuthRepositoryInterface
{
    public function save(User $user): string;
    public function getUsers(): array;
    public function getUserByEmail(string $email): User;
    public function getUserById(string $id): User;
    public function getUserByRole(int $role): array;
    public function creerUser(string $email, string $password, int $role): User;

}