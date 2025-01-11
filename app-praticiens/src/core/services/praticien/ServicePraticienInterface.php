<?php

namespace api_praticien\core\services\praticien;

use api_praticien\core\dto\InputPraticienDTO;
use api_praticien\core\dto\PraticienDTO;
use api_praticien\core\dto\SpecialiteDTO;

interface ServicePraticienInterface
{

    public function createPraticien(InputPraticienDTO $p): PraticienDTO;
    public function getPraticienById(string $id): PraticienDTO;
    public function getSpecialiteById(string $id): SpecialiteDTO;

    public function getAllPraticien(): array;
}
