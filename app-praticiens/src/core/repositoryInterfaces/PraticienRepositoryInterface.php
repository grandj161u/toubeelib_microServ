<?php

namespace api_praticien\core\repositoryInterfaces;

use api_praticien\core\domain\entities\praticien\Praticien;
use api_praticien\core\domain\entities\praticien\Specialite;

interface PraticienRepositoryInterface
{

    public function getSpecialiteById(string $id): Specialite;
    public function save(Praticien $praticien): string;
    public function getPraticienById(string $id): Praticien;

    public function getAllPraticien(): array;
}
