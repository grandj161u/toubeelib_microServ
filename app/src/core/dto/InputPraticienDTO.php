<?php

namespace toubeelib\core\dto;
use InvalidArgumentException;

class InputPraticienDTO extends DTO
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected string $specialite;


    public function __construct(string $nom, string $prenom, string $tel,string $adresse, string $specialite) {
        if (!is_string($nom) || !ctype_alnum($nom)) {
            throw new InvalidArgumentException("Le nom doit être une chaîne alphanumérique.");
        }

        if (!is_string($prenom) || !ctype_alnum($prenom)) {
            throw new InvalidArgumentException("Le prénom doit être une chaîne alphanumérique.");
        }

        if (!is_string($tel) || !ctype_alnum($tel)) {
            throw new InvalidArgumentException("Le numéro de téléphone doit être une chaîne alphanumérique.");
        }

        if (!is_string($adresse) || !ctype_alnum($adresse)) {
            throw new InvalidArgumentException("L'adresse doit être une chaîne alphanumérique.");
        }

        if (!is_string($specialite) || !ctype_alnum($specialite)) {
            throw new InvalidArgumentException("La spécialité doit être une chaîne alphanumérique.");
        }



        $this->nom = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');
        $this->prenom = htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8');
        $this->adresse = htmlspecialchars($adresse, ENT_QUOTES, 'UTF-8');
        $this->tel = htmlspecialchars($tel, ENT_QUOTES, 'UTF-8');
        $this->specialite = htmlspecialchars($specialite, ENT_QUOTES, 'UTF-8');
    }

}