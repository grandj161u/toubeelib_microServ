<?php

namespace toubeelib\core\services\rdv;

use DateTimeImmutable;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;

interface ServiceRdvInterface{

    public function getRdvById(String $id): RdvDTO;

    public function modifierRdv(String $id, ?String $idSpecialite, ?String $idPatient): RdvDTO;

    public function creerRdv(InputRdvDTO $inputRdvDTO): RdvDTO;

    public function annulerRdv(String $id): RdvDTO;

    public function getRdvByPatient(String $id): array;
}