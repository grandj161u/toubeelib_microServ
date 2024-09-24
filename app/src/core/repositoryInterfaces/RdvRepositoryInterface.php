<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\rdv\Rdv;

interface RdvRepositoryInterface
{

    public function save(Rdv $rdv): string;
    public function getRdvById(string $id): Rdv;
    // public function getRdvByPraticienId(string $id): array;
    // public function getRdvByPatientId(string $id): array;

    public function modifierRdv(string $id, string|null $specialite, string|null $idPatient): Rdv;
}