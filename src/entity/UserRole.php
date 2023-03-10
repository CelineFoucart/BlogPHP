<?php

declare(strict_types=1);

namespace App\Entity;

final class UserRole extends AbstractEntity
{
    private ?string $name = null;

    private ?string $alias = null;

    /**
     * Get the value of name.
     *
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name.
     *
     * @param ?string $name
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of alias.
     *
     * @return ?string
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * Set the value of alias.
     *
     * @param ?string $alias
     */
    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
