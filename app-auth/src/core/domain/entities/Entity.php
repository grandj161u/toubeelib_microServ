<?php

namespace api_auth\core\domain\entities;

abstract class Entity
{

    protected ?string $ID=null;
    public function __get(string $name): mixed
    {
        return property_exists($this, $name) ? $this->$name : throw new \Exception(static::class . ": Property $name does not exist");
    }

    public function __set($name, $value): void
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            throw new \Exception(static::class . ": Property $name does not exist");
        }
    }

    public function setID(string $ID): void
    {
        $this->ID = $ID;
    }

    public function getID(): ?string
    {
        return $this->ID;
    }

}