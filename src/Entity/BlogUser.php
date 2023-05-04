<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

/**
 * BlogUser represents a user, persited in the database.
 */
final class BlogUser extends AbstractEntity
{
    private ?string $username = null;

    private ?string $firstname = null;

    private ?string $lastname = null;

    private ?string $password = null;

    private ?string $email = null;

    private ?UserRole $role = null;

    private int $attempts = 0;

    private ?DateTime $lastAttempt = null;

    /**
     * Gets the value of username.
     *
     * @return ?string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Sets the value of username.
     *
     * @param ?string $username
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Gets the value of firstName.
     *
     * @return ?string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Sets the value of firstName.
     *
     * @param ?string $firstName
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Gets the value of lastName.
     *
     * @return ?string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Sets the value of lastName.
     *
     * @param ?string $lastName
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Gets the value of password.
     *
     * @return ?string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets the value of password.
     *
     * @param ?string $password
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets the value of email.
     *
     * @return ?string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the value of email.
     *
     * @param ?string $email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the value of role.
     */
    public function getRole(): ?UserRole
    {
        if (!$this->role) {
            $this->role = new UserRole();
        }

        return $this->role;
    }

    /**
     * Sets the value of role.
     */
    public function setRole(?UserRole $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Gets the value of lastAttempt.
     *
     * @return ?DateTime
     */
    public function getLastAttempt(): ?DateTime
    {
        return $this->lastAttempt;
    }

    /**
     * Sets the value of lastAttempt.
     *
     * @param DateTime|string|null $lastAttempt
     */
    public function setLastAttempt($lastAttempt): self
    {
        if ($lastAttempt instanceof DateTime) {
            $this->lastAttempt = $lastAttempt;
        } elseif (is_string($lastAttempt)) {
            $this->lastAttempt = new DateTime($lastAttempt);
        }

        return $this;
    }

    /**
     * Gets the value of attempts.
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * Sets the value of attempts.
     */
    public function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function __toString()
    {
        return $this->username;
    }
}
