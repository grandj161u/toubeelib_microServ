<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\dto\RdvDTO;

interface ServiceRdvInterface{

    public function getRdvById(String $id): RdvDTO;

}