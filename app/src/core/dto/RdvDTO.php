<?php 

namespace toubeelib\core\dto;

use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\dto\DTO;

class RdvDTO extends DTO
{
    protected string $ID;
    protected string $idPraticien;
    protected string $idPatient;
    protected string $horaire;
    protected string $idSpecialite;

    public function __construct(Rdv $r)
    {
        $this->ID = $r->getID();
        $this->idPraticien = $r->idPraticien;
        $this->idPatient = $r->idPatient;
        $this->horaire = $r->horaire;
        $this->idSpecialite = $r->idSpecialite;
    }   
}