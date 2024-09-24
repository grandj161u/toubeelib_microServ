<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\dto\RdvDTO;

interface ServiceRdvInterface{

    public function getRdvById(String $id): RdvDTO;

    public function modifierRdv(String $id, ?String $specialite, ?String $idPatient): RdvDTO;

}