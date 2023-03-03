<?php

namespace App\Entity;

abstract class AbstractEntity
{
    private ?int $id = null;

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
}
