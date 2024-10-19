<?php
namespace toubeelib\infrastructure\repositories;

use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use Ramsey\Uuid\Uuid;
use toubeelib\core\repositoryInterfaces\RepositoryDatabaseErrorException;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\domain\entities\rdv\Rdv;

class PDORdvRepository implements RdvRepositoryInterface {

    private \PDO $pdoRdv;
    private \PDO $pdoPatient;
    private \PDO $pdoPraticien;

    public function __construct(\PDO $pdoR, \PDO $pdoPa, \PDO $pdoPra){
        $this->pdoRdv = $pdoR;
        $this->pdoPatient = $pdoPa;
        $this->pdoPraticien = $pdoPra;
    }

    public function save(Rdv $rdv): string {
        $query = 'INSERT INTO rdv (id, id_praticien, id_patient, id_spe, date_rdv, statut, type_rdv) VALUES (:id, :id_pra, :id_pat, :id_spec, :date_rdv, :statut, :type_rdv)';
        try {
            $stmt = $this->pdoRdv->prepare($query);
            $stmt->bindValue(':id', $rdv->getID(), \PDO::PARAM_STR);
            $stmt->bindValue(':id_pra', $rdv->__get('idPraticien'), \PDO::PARAM_STR);
            $stmt->bindValue(':id_pat', $rdv->__get('idPatient'), \PDO::PARAM_STR);
            $stmt->bindValue(':id_spec', $rdv->__get('idSpecialite'), \PDO::PARAM_STR);
            $stmt->bindValue(':date_rdv', $rdv->__get('horaire')->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
            $stmt->bindValue(':statut', $rdv->__get('statut'), \PDO::PARAM_STR);
            $stmt->bindValue(':type_rdv', $rdv->__get('type'), \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while saving rdv $rdv ' . $e->getMessage());
        }
        return $rdv->getID();
    }

    public function getRdvs(): array {
        $query = 'SELECT * FROM rdv';
        try {
            $stmt = $this->pdoRdv->prepare($query);
            $stmt->execute();
            $rdvs = $stmt->fetchAll();
            if(!$rdvs){
                throw new RepositoryEntityNotFoundException('Rdvs not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching rdvs');
        }
        $rdvsArray = [];
        foreach($rdvs as $rdv){
            $r = new Rdv($rdv['id_praticien'], $rdv['id_patient'], new \DateTimeImmutable($rdv['date_rdv']), $rdv['id_spe'], $rdv['type_rdv'], $rdv['statut']);
            $r->setID($rdv['id']);
            $rdvsArray[] = $r;
        }
        return $rdvsArray;
    }

    public function getRdvById(string $id): Rdv {
        $query = 'SELECT * FROM rdv WHERE id = :id';
        try {
            $stmt = $this->pdoRdv->prepare($query);
            $stmt->bindParam(':id',$id, \PDO::PARAM_STR);
            $stmt->execute();
            $rdv = $stmt->fetch();
            if(!$rdv){
                throw new RepositoryEntityNotFoundException('Rdv not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching rdv');
        }
        $r = new Rdv($rdv['id_praticien'], $rdv['id_patient'], new \DateTimeImmutable($rdv['date_rdv']), $rdv['id_spe'], $rdv['type_rdv'], $rdv['statut']);
        $r->setID($rdv['id']);
        return $r;
    }
    
    public function getRdvByPatient(string $id): array {
        $query = 'SELECT * FROM rdv WHERE id_patient = :id';
        try {
            $stmt = $this->pdoRdv->prepare($query);
            $stmt->bindParam(':id',$id, \PDO::PARAM_STR);
            $stmt->execute();
            $rdvs = $stmt->fetchAll();
            if(!$rdvs){
                throw new RepositoryEntityNotFoundException('Rdvs not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching rdvs');
        }
        $rdvsArray = [];
        foreach($rdvs as $rdv){
            $r = new Rdv($rdv['id_praticien'], $rdv['id_patient'], new \DateTimeImmutable($rdv['date_rdv']), $rdv['id_spe'], $rdv['type_rdv'], $rdv['statut']);
            $r->setID($rdv['id']);
            $rdvsArray[] = $r;
        }
        return $rdvsArray;
    }

    public function getRdvByPraticienId(string $id): array {
        $query = 'SELECT * FROM rdv WHERE id_praticien = :id';
        try {
            $stmt = $this->pdoRdv->prepare($query);
            $stmt->bindParam(':id',$id, \PDO::PARAM_STR);
            $stmt->execute();
            $rdvs = $stmt->fetchAll();
            if(!$rdvs){
                throw new RepositoryEntityNotFoundException('Rdvs not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching rdvs');
        }
        $rdvsArray = [];
        foreach($rdvs as $rdv){
            $r = new Rdv($rdv['id_praticien'], $rdv['id_patient'], new \DateTimeImmutable($rdv['date_rdv']), $rdv['id_spe'], $rdv['type_rdv'], $rdv['statut']);
            $r->setID($rdv['id']);
            $rdvsArray[] = $r;
        }
        return $rdvsArray;
    }

    public function modifierRdv(string $id, string|null $idSpecialite, string|null $idPatient): Rdv {
        if($idPatient != null){
            $query = 'SELECT * FROM patient WHERE id = :id';
            try {
                $stmt = $this->pdoPatient->prepare($query);
                $stmt->bindParam(':id',$idPatient, \PDO::PARAM_STR);
                $stmt->execute();
                $pa = $stmt->fetch();
                if(!$pa){
                    throw new RepositoryEntityNotFoundException('Patient not found');
            }
            } catch (\PDOException $e) {
                throw new RepositoryDatabaseErrorException('Error while fetching rdv');
            }
        }
        if($idSpecialite != null){
            $query = 'SELECT * FROM specialite WHERE id = :id';
            try {
                $stmt = $this->pdoPraticien->prepare($query);
                $stmt->bindParam(':id',$idSpecialite, \PDO::PARAM_STR);
                $stmt->execute();
                $spec = $stmt->fetch();
                if(!$spec){
                    throw new RepositoryEntityNotFoundException('Specialite not found');
                }
            } catch (\PDOException $e) {
                throw new RepositoryDatabaseErrorException('Error while fetching specialite');
            }
        }   

        $query = 'UPDATE rdv SET';
        if($idSpecialite === null && $idPatient != null) {
            $query .= ' id_patient = :id_patient where id = :id';
        } else if($idSpecialite !== null && $idPatient === null) {
            $query .= ' id_spe = :id_spe where id = :id';
        } else if($idSpecialite !== null && $idPatient !== null) {
            $query .= ' id_spe = :id_spe, id_patient = :id_patient where id = :id';
        } else {
            throw new RepositoryDatabaseErrorException('No data to update');
        }
        try {
            $stmt = $this->pdoRdv->prepare($query);
            $stmt->bindParam(':id',$id, \PDO::PARAM_STR);
            if($idSpecialite !== null){
                $stmt->bindParam(':id_spe',$idSpecialite, \PDO::PARAM_STR);
            }
            if($idPatient !== null){
                $stmt->bindParam(':id_patient',$idPatient, \PDO::PARAM_STR);
            }
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while updating rdv');
        }
        return $this->getRdvById($id);
    }

    public function creerRdv(string $idPraticien, string $idPatient, \DateTimeImmutable $horaire, string $idSpecialite, string $type, string $statut): Rdv {
        $query = 'SELECT * FROM patient WHERE id = :id';
        try {
            $stmt = $this->pdoPatient->prepare($query);
            $stmt->bindParam(':id',$idPatient, \PDO::PARAM_STR);
            $stmt->execute();
            $pa = $stmt->fetch();
            if(!$pa){
                throw new RepositoryEntityNotFoundException('Patient not found');
            }
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException('Error while fetching patient');
        }

        $rdv = new Rdv($idPraticien, $idPatient, $horaire, $idSpecialite, $type, $statut);
        $rdv->setID(Uuid::uuid4()->toString());
        print_r($rdv);
        $this->save($rdv);
        return $rdv;
    }

    public function annulerRdv(string $id): Rdv {
        $query = "UPDATE rdv SET statut = 'annule' where id = :id";
        try {
            $stmt = $this->pdoRdv->prepare($query);
            $stmt->bindParam(':id',$id, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException($e->getMessage());
        }
        return $this->getRdvById($id);
    }
}