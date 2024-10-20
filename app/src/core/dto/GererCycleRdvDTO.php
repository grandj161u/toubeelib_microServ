<?php

namespace toubeelib\core\dto;

use InvalidArgumentException;

class GererCycleRdvDTO extends DTO
{
    protected string $statut;

    public function __construct($statut) {

        if ($statut === "annuler") {
            throw new InvalidArgumentException("Le statut du RDV ne peut pas être annuler de cette manière.");
        }

        $validStatus = ['honorer', 'non_honorer', 'payer'];
        if (!in_array($statut, $validStatus, true)) {
            throw new InvalidArgumentException("Le statut du RDV n'est pas valide.");
        }

        $this->statut = $statut;
    }
}