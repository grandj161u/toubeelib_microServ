<?php

namespace toubeelib\core\domain\entities\rdv;

use toubeelib\core\domain\entities\Entity;
use toubeelib\core\dto\RdvDTO;

class Rdv extends Entity
{
    protected string $idPraticien;
    protected string $idPatient;
    protected string $horaire;
    protected string $idSpecialite;

    public function __construct($praticien, $patient, $horaire, $specialite)
    {
        $this->idPraticien = $praticien;
        $this->idPatient = $patient;
        $this->horaire = $horaire;
        $this->idSpecialite = $specialite;
    }

    public function toDTO() : RdvDTO {
        return new RdvDTO($this);
    }
}