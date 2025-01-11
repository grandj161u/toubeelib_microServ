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

    public function __construct($ID, $idPraticien, $idPatient, $horaire, $idSpecialite, $type, $statut)
    {
        $this->ID = $ID;
        $this->idPraticien = $idPraticien;
        $this->idPatient = $idPatient;
        $this->horaire = $horaire;
        $this->idSpecialite = $idSpecialite;
        $this->type = $type;
        $this->statut = $statut;
    }

    public function jsonSerialize(): array{
        return [
            "ID" => $this->ID,
            "idPraticien" => $this->idPraticien,
            "idPatient" => $this->idPatient,
            "horaire" => $this->horaire,
            "idSpecialite" => $this->idSpecialite,
            "type" => $this->type,
            "statut" => $this->statut
        ];
    }
}