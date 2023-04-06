<?php

declare(strict_types=1);

namespace App\Entity;

final class UserRole extends AbstractEntity
{
    private ?string $name = null;

    private ?string $alias = null;

    /**
     * Get the value of name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name.
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of alias.
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * Set the value of alias.
     */
    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
