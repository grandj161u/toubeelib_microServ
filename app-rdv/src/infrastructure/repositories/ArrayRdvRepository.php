<?php

namespace api_rdv\infrastructure\repositories;

use Ramsey\Uuid\Uuid;
use api_rdv\core\domain\entities\rdv\Rdv;
use api_rdv\core\dto\PraticienDTO;
use api_rdv\core\repositoryInterfaces\RdvRepositoryInterface;
use api_rdv\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayRdvRepository implements RdvRepositoryInterface
{
    private array $rdvs = [];

    public function __construct()
    {
        $r1 = new Rdv('p1', 'pa1', \DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-09-02 09:00'), 'A', '1', 'confirmer');
        $r1->setID('r1');
        $r2 = new Rdv('p1', 'pa1', \DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-09-02 10:00'), 'A', '5', 'a_payer');
        $r2->setID('r2');
        $r3 = new Rdv('p2', 'pa1', \DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-09-02 09:30'), 'A', '4', 'en_attente');
        $r3->setID('r3');
        $r4 = new Rdv('p2', 'pa2', \DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-09-02 10:30'), 'C', '3', 'confirmer');
        $r4->setID('r4');

        $this->rdvs  = ['r1' => $r1, 'r2' => $r2, 'r3' => $r3, 'r4' => $r4];
    }

    public function save(Rdv $rdv): string
    {
        $ID = Uuid::uuid4()->toString();
        // $rdv->setID($ID);
        $rdv->setID('r4');
        $this->rdvs[$ID] = $rdv;
        return $ID;
    }

    public function getRdvs(): array
    {
        return $this->rdvs;
    }

    public function getRdvByPraticienId(string $id): array
    {
        $rdvs = [];
        foreach ($this->rdvs as $rdv) {
            if ($rdv->__get('idPraticien') === $id) {
                $rdvs[] = $rdv;
            }
        }
        return $rdvs;
    }

    public function getRdvById(string $id): Rdv
    {
        $rdv = $this->rdvs[$id] ??
            throw new RepositoryEntityNotFoundException("Rdv $id not found");

        return $rdv;
    }

    public function modifierRdv(string $id, string|null $idSpecialite, string|null $idPatient): Rdv
    {
        $rdv = $this->rdvs[$id] ??
            throw new RepositoryEntityNotFoundException("Rdv $id not found");

        if ($idSpecialite) {
            $rdv->__set('idSpecialite', $idSpecialite);
        }
        if ($idPatient) {
            $rdv->__set('idPatient', $idPatient);
        }

        return $rdv;
    }

    public function creerRdv(string $idPraticien, string $idPatient, \DateTimeImmutable $horaire, string $idSpecialite, string $type, string $statut): Rdv
    {
        $rdv = new Rdv($idPraticien, $idPatient, $horaire, $idSpecialite, $type, $statut);

        $ID = $this->save($rdv);
        return $this->rdvs[$ID];
    }

    public function getRdvByPatient(string $id): array
    {
        $rdvs = [];
        foreach ($this->rdvs as $rdv) {
            if ($rdv->__get('idPatient') === $id) {
                $rdvs[] = $rdv;
            }
        }
        return $rdvs;
    }

    public function annulerRdv(string $id): Rdv
    {
        $rdv = $this->rdvs[$id] ??
            throw new RepositoryEntityNotFoundException("Rdv $id not found");

        $rdv->__set('statut', 'annuler');
        return $rdv;
    }

    public function GererCycleRdv(string $id, string $statut): Rdv
    {
        $rdv = $this->rdvs[$id] ??
            throw new RepositoryEntityNotFoundException("Rdv $id not found");

        $rdv->__set('statut', $statut);
        return $rdv;
    }
}
