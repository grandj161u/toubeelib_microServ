<?php

namespace api_auth\infrastructure\repositories;

use api_auth\core\domain\entities\authentification\User;
use api_auth\core\repositoryInterfaces\AuthRepositoryInterface;
use api_auth\core\repositoryInterfaces\RepositoryDatabaseErrorException;
use api_auth\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use Ramsey\Uuid\Uuid;

class PDOAuthRepository implements AuthRepositoryInterface
{
    private \PDO $pdoAuth;

    public function __construct($pdo)
    {
        $this->pdoAuth = $pdo;
    }

    public function save(User $user): string
    {
        $query = 'INSERT INTO users (id, email, password, role) VALUES (:id, :email, :pwd, :role)';
        try {
            $stmt = $this->pdoAuth->prepare($query);
            $stmt->bindValue(':id', $user->getID(), \PDO::PARAM_STR);
            $stmt->bindValue(':email', $user->email, \PDO::PARAM_STR);
            $stmt->bindValue(':pwd', password_hash($user->password, PASSWORD_DEFAULT), \PDO::PARAM_STR);
            $stmt->bindValue(':role', $user->role, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while saving rdv $rdv ' . $e->getMessage());
        }
        return $user->getID();
    }

    public function getUsers(): array
    {
        $query = 'SELECT * FROM users';
        try {
            $stmt = $this->pdoAuth->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchAll();
            if (!$users) {
                throw new RepositoryEntityNotFoundException('Users not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching users');
        }
        $usersArray = [];
        foreach ($users as $user) {
            $u = new User($user['email'], $user['password'], $user['role']);
            $u->setID($user['id']);
            $usersArray[] = $u;
        }
        return $usersArray;
    }

    public function getUserByEmail(string $email): User
    {
        $query = 'SELECT * FROM users WHERE email = :email';
        try {
            $stmt = $this->pdoAuth->prepare($query);
            $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();
            if (!$user) {
                throw new RepositoryEntityNotFoundException('User not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching user');
        }
        $u = new User($user['email'], $user['password'], $user['role']);
        $u->setID($user['id']);
        return $u;
    }

    public function getUserById(string $id): User
    {
        $query = 'SELECT * FROM users WHERE id = :id';
        try {
            $stmt = $this->pdoAuth->prepare($query);
            $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();
            if (!$user) {
                throw new RepositoryEntityNotFoundException('User not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching user');
        }
        $u = new User($user['email'], $user['password'], $user['role']);
        $u->setID($user['id']);
        return $u;
    }
    public function getUserByRole(int $role): array
    {
        $query = 'SELECT * FROM users WHERE role = :role';
        try {
            $stmt = $this->pdoAuth->prepare($query);
            $stmt->bindValue(':role', $role, \PDO::PARAM_STR);
            $stmt->execute();
            $users = $stmt->fetchAll();
            if (!$users) {
                throw new RepositoryEntityNotFoundException('Users not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching users');
        }
        $usersArray = [];
        foreach ($users as $user) {
            $u = new User($user['email'], $user['password'], $user['role']);
            $u->setID($user['id']);
            $usersArray[] = $u;
        }
        return $usersArray;
    }

    public function creerUser(string $email, string $password, int $role): User
    {
        $user = new User($email, $password, $role);
        $user->setID(Uuid::uuid4()->toString());
        $this->save($user);
        return $user;
    }
}
