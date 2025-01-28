<?php

namespace api_rdv\core\services\rdv;

use DateTimeImmutable;
use api_rdv\core\dto\InputRdvDTO;
use api_rdv\core\dto\RdvDTO;
use api_rdv\core\dto\ModifyRdvDTO;
use api_rdv\core\dto\GererCycleRdvDTO;

interface ServiceRdvInterface
{

    public function getRdvs(): array;
    public function getRdvById(String $id): RdvDTO;
    public function modifierRdv(ModifyRdvDTO $modifyRdvDTO, String $ID): RdvDTO;
    public function creerRdv(InputRdvDTO $inputRdvDTO): RdvDTO;
    public function annulerRdv(String $id): RdvDTO;
    public function getRdvByPatient(String $id): array;
    public function getRdvByPraticienId(string $id): array;
    public function getDisponibiliterPraticien(string $idPraticien, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): array;
    public function GererCycleRdv(GererCycleRdvDTO $gererCycleRdvDTO, string $ID): RdvDTO;
    public function getPlanningPraticien(string $idPraticien, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): array;
    public function sendMessageRdv(string $message, string $idRdv);
}
