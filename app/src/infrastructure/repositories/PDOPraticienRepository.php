<?php

namespace toubeelib\infrastructure\repositories;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryDatabaseErrorException;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\domain\entities\praticien\Praticien;
use Ramsey\Uuid\Uuid;

class PDOPraticienRepository implements PraticienRepositoryInterface {

    private \PDO $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }   

    public function getSpecialiteById(string $id): Specialite{
        $query = 'SELECT * FROM specialite WHERE id = :id';
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id',$id, \PDO::PARAM_STR);
            $stmt->execute();
            $specialite = $stmt->fetch();
            if(!$specialite){
                throw new RepositoryEntityNotFoundException('Specialite not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching specialite');
        }
        return new Specialite($specialite['id'], $specialite['label'], $specialite['description']);
    }

    public function save(Praticien $praticien): string {
        $query = 'INSERT INTO praticien (ID, nom, prenom, specialite_id) VALUES (:ID, :nom, :prenom, :specialite_id)';
        if($praticien->getID() === null){
            $praticien->setID(Uuid::uuid4()->toString());
        }
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':ID', $praticien->getID(), \PDO::PARAM_STR);
            $stmt->bindValue(':nom', $praticien->__get('nom'), \PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $praticien->__get('prenom'), \PDO::PARAM_STR);
            $stmt->bindValue(':specialite_id', $praticien->__get('specialite')->getID(), \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while saving praticien');
        }
        return $this->pdo->lastInsertId();
    }

    public function getPraticienById(string $id): Praticien {
        $query = 'SELECT * FROM praticien WHERE id = :id';
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id',$id, \PDO::PARAM_STR);
            $stmt->execute();
            $praticien = $stmt->fetch();
            if(!$praticien){
                throw new RepositoryEntityNotFoundException('Praticien not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching praticien');
        }
        $specialite = $this->getSpecialiteById($praticien['specialite_id']);
        $p = new Praticien($praticien['nom'], $praticien['prenom'], $praticien['adresse'], $praticien['tel']);
        $p->setSpecialite($specialite);
        $p->setID($praticien['id']);
        return $p;
    }
}