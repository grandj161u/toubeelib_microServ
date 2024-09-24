<?php

namespace toubeelib\infrastructure\repositories;

use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayRdvRepository implements RdvRepositoryInterface
{
    private array $rdvs = [];

    public function __construct() {
            $r1 = new Rdv('p1', 'pa1', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 09:00'), 'A', '1', 'confirmer');
            $r1->setID('r1');
            $r2 = new Rdv('p1', 'pa1', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 10:00'), 'A', '5', 'a payer');
            $r2->setID('r2');
            $r3 = new Rdv('p2', 'pa1', \DateTimeImmutable::createFromFormat('Y-m-d H:i','2024-09-02 09:30'), 'A', '4', 'en attente');
            $r3->setID('r3');

        $this->rdvs  = ['r1'=> $r1, 'r2'=>$r2, 'r3'=> $r3 ];
    }

    public function save(Rdv $rdv): string {
        $ID = Uuid::uuid4()->toString();
        $rdv->setID($ID);
        $this->rdvs[$ID] = $rdv;
        return $ID;
    }

    public function getRdvById(string $id): Rdv{
        $rdv = $this->rdvs[$id] ??
            throw new RepositoryEntityNotFoundException("Rdv $id not found");

        return $rdv;
    }

    public function modifierRdv(string $id, string|null $specialite, string|null $idPatient): Rdv{
        $rdv = $this->rdvs[$id] ??
            throw new RepositoryEntityNotFoundException("Rdv $id not found");

        if($specialite){
            $rdv->__set('idSpecialite',$specialite);

        }
        if($idPatient){
            $rdv->__set('idPatient',$idPatient);
        }

        return $rdv;
    }
}