<?php

namespace api_rdv\core\repositoryInterfaces;

use api_rdv\core\domain\entities\rdv\Rdv;
use api_rdv\core\dto\PraticienDTO;

interface RdvRepositoryInterface
{

    public function save(Rdv $rdv): string;
    public function getRdvs(): array;
    public function getRdvById(string $id): Rdv;
    public function getRdvByPraticienId(string $id): array;
    public function getRdvByPatient(string $id): array;

    public function modifierRdv(string $id, string|null $idSpecialite, string|null $idPatient): Rdv;

    public function creerRdv(string $idPraticien, string $idPatient, \DateTimeImmutable $horaire, string $idSpecialite, string $type, string $statut): Rdv;

    public function annulerRdv(string $id): Rdv;

    public function GererCycleRdv(string $id, string $statut): Rdv;
}
