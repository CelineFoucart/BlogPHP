<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * UserRole represents a user role, needed to handle permissions.
 * By default, there are two roles: ROLE_ADMIN and ROLE_USER.
 */
final class UserRole extends AbstractEntity
{
    /**
     * @var string|null the unique role name, for example ROLE_USER
     */
    private ?string $name = null;

    /**
     * @var string|null the alias will be displayed in the templates
     */
    private ?string $alias = null;

    /**
     * Gets the value of name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of alias.
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * Sets the value of alias.
     */
    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
