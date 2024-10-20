<?php

namespace toubeelib\core\services\rdv;

use DateTimeImmutable;
use toubeelib\core\dto\InputRdvDTO;
use toubeelib\core\dto\RdvDTO;
use toubeelib\core\dto\ModifyRdvDTO;

interface ServiceRdvInterface{

    public function getRdvs(): array;
    public function getRdvById(String $id): RdvDTO;
    public function modifierRdv(ModifyRdvDTO $modifyRdvDTO, String $ID): RdvDTO;
    public function creerRdv(InputRdvDTO $inputRdvDTO): RdvDTO;
    public function annulerRdv(String $id): RdvDTO;
    public function getRdvByPatient(String $id): array;
    public function getRdvByPraticienId(string $id): array;

    public function getDisponibiliterPraticien(string $idPraticien, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): array;
}