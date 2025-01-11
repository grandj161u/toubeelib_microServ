<?php

namespace api_praticien\core\domain\entities\praticien;

use api_praticien\core\domain\entities\Entity;
use api_praticien\core\dto\PraticienDTO;

class Praticien extends Entity
{
    protected string $nom;
    protected string $prenom;
    protected string $tel;
    protected string $adresse;
    protected ?Specialite $specialite = null;

    public function __construct(string $nom, string $prenom, string $tel, string $adresse)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->tel = $tel;
        $this->adresse = $adresse;
    }


    public function setSpecialite(Specialite $specialite): void
    {
        $this->specialite = $specialite;
    }

    public function toDTO(): PraticienDTO
    {
        return new PraticienDTO($this);
    }
}
