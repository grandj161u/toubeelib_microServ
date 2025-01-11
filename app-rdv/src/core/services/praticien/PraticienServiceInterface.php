<?php

namespace api_rdv\core\services\praticien;

use api_rdv\core\dto\PraticienDTO;
use api_rdv\core\dto\SpecialiteDTO;

interface PraticienServiceInterface
{
    public function getPraticienById(string $id): PraticienDTO;
    public function getAllPraticiens(): array;
    public function getSpecialiteById(string $id): SpecialiteDTO;
}
