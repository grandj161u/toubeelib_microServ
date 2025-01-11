<?php

namespace api_rdv\core\dto;

use InvalidArgumentException;

class ModifyRdvDTO extends DTO
{
    protected ?string $idPatient;
    protected ?string $idSpecialite;

    public function __construct(string|null $idPatient, string|null $idSpecialite)
    {

        $patient = null;
        $specialite = null;

        if ($idPatient != null) {
            if (!is_string($idPatient) || !ctype_alnum($idPatient)) {
                throw new InvalidArgumentException("L'idPatient doit être une chaîne alphanumérique.");
            }
            $patient = htmlspecialchars($idPatient, ENT_QUOTES, 'UTF-8');
        }

        if ($idSpecialite != null) {
            if (!is_string($idSpecialite) || !ctype_alnum($idSpecialite)) {
                throw new InvalidArgumentException("L'idSpecialite doit être une chaîne alphanumérique.");
            }
            $specialite = htmlspecialchars($idSpecialite, ENT_QUOTES, 'UTF-8');
        }

        $this->idPatient = $patient;
        $this->idSpecialite = $specialite;
    }
}
