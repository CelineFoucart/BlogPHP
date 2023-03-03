<?php

namespace App\Entity;

use DateTime;

class User extends AbstractEntity
{
    private ?string $username = null;

    private ?string $firstName = null;

    private ?string $lastName = null;

    private ?string $password = null;

    private ?string $email = null;

    private ?DateTime $createdAt = null;

    private ?string $role = 'ROLE_USER';

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
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName.
     *
     * @param ?string $firstName
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName.
     *
     * @return ?string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName.
     *
     * @param ?string $lastName
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

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
     *
     * @return ?string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Set the value of role.
     *
     * @param ?string $role
     */
    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
