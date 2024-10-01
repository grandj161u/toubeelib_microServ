<?php

namespace toubeelib\core\services\rdv;

use DateTimeImmutable;
use toubeelib\core\dto\RdvDTO;

interface ServiceRdvInterface{

    public function getRdvById(String $id): RdvDTO;

    public function modifierRdv(String $id, ?String $idSpecialite, ?String $idPatient): RdvDTO;

    public function creerRdv(String $idPraticien, String $idPatient, DateTimeImmutable $horaire, String $idSpecialite, String $type, String $statut);

}