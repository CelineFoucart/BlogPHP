<?php

namespace App\Entity;

/**
 * AbstractEntity provides methodes required in all entities.
 */
abstract class AbstractEntity
{
    /**
     * @var int|null the unique identifier of the entity in the database
     */
    protected ?int $id = null;

    /**
     * Gets the value of id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Hydrates dynamically the entity.
     */
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
