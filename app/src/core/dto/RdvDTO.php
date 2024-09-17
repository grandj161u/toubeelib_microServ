<?php 

namespace toubeelib\core\dto;

use DateTimeImmutable;
use toubeelib\core\domain\entities\rdv\Rdv;
use toubeelib\core\dto\DTO;

class RdvDTO extends DTO
{
    protected string $ID;
    protected string $idPraticien;
    protected string $idPatient;
    protected DateTimeImmutable $horaire;
    protected string $idSpecialite;
    protected string $type;
    protected string $statut;

    public function __construct(Rdv $r)
    {
        $this->ID = $r->getID();
        $this->idPraticien = $r->idPraticien;
        $this->idPatient = $r->idPatient;
        $this->horaire = $r->horaire;
        $this->idSpecialite = $r->idSpecialite;
        $this->type = $r->type;
        $this->statut = $r->statut;
    }   
}