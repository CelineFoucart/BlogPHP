<?php

namespace App\Entity;

abstract class AbstractEntity
{
    protected ?int $id = null;

    /**
     * Get the value of id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id.
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function __set($name, $value)
    {
        $method = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $parts = explode('_', $name, 2);

            if (empty($parts)) {
                return;
            }

            $class = $parts[0];

            if (property_exists($this, $class)) {
                $classGetter = 'get'.ucwords($class);
                $propertyMethod = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $parts[1])));

                $this->$classGetter()->$propertyMethod($value);
            }
        }
    }
}
