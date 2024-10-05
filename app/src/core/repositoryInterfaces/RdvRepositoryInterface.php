<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\dto\PraticienDTO;

interface RdvRepositoryInterface
{

    public function save(Rdv $rdv): string;
    public function getRdvById(string $id): Rdv;
    // public function getRdvByPraticienId(string $id): array;
    public function getRdvByPatient(string $id): array;

    public function modifierRdv(string $id, string|null $idSpecialite, string|null $idPatient): Rdv;

    public function creerRdv(string $idPraticien, string $idPatient, \DateTimeImmutable $horaire, string $idSpecialite, string $type, string $statut): Rdv;

    public function annulerRdv(string $id): Rdv;
}