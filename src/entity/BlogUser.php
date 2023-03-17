<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

final class BlogUser extends AbstractEntity
{
    private ?string $username = null;

    private ?string $firstname = null;

    private ?string $lastname = null;

    private ?string $password = null;

    private ?string $email = null;

    private ?DateTime $createdAt = null;

    private ?UserRole $role = null;

    /**
     * Get the value of username.
     *
     * @return ?string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set the value of username.
     *
     * @param ?string $username
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of firstName.
     *
     * @return ?string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstName.
     *
     * @param ?string $firstName
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of lastName.
     *
     * @return ?string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastName.
     *
     * @param ?string $lastName
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of password.
     *
     * @return ?string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the value of password.
     *
     * @param ?string $password
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of createdAt.
     *
     * @return ?DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt.
     *
     * @param ?DateTime $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return ?string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email.
     *
     * @param ?string $email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of role.
     */
    public function getRole(): ?UserRole
    {
        if (!$this->role) {
            $this->role = new UserRole();
        }

        return $this->role;
    }

    /**
     * Set the value of role.
     */
    public function setRole(?UserRole $role): self
    {
        $this->role = $role;

        return $this;
    }
}
