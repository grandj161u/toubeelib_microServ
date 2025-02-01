<?php

namespace api_praticien\core\dto;

use api_praticien\core\domain\entities\praticien\Praticien;
use api_praticien\core\dto\DTO;
use api_praticien\core\domain\entities\praticien\Specialite;

class PraticienDTO extends DTO
{
    protected string $ID;
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected Specialite $specialite;

    public function __construct(Praticien $p)
    {
        $this->ID = $p->getID();
        $this->nom = $p->nom;
        $this->prenom = $p->prenom;
        $this->adresse = $p->adresse;
        $this->tel = $p->tel;
        $this->specialite = $p->specialite;
    }

    public function jsonSerialize(): array
    {
        $specialiteDTO = $this->specialite->toDTO();
        return [
            'ID' => $this->ID,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'adresse' => $this->adresse,
            'tel' => $this->tel,
            'specialite' => $specialiteDTO
        ];
    }
}