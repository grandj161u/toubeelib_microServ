<?php

namespace api_praticien\core\dto;

use api_praticien\core\dto\DTO;
use api_praticien\core\domain\entities\praticien\Specialite;

class SpecialiteDTO extends DTO
{
    protected string $ID;
    protected string $label;
    protected string $description;

    public function __construct(Specialite $s)
    {
        $this->ID = $s->getID();
        $this->label = $s->label;
        $this->description = $s->description;
    }

    public function jsonSerialize(): array
    {
        return [
            'ID' => $this->ID,
            'label' => $this->label,
            'description' => $this->description
        ];
    }
}
