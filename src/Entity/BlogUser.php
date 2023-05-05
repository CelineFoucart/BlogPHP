<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

/**
 * BlogUser represents a user, persited in the database.
 */
final class BlogUser extends AbstractEntity
{
    /**
     * @var string|null the user's name
     */
    private ?string $username = null;

    /**
     * @var string|null the firt name of the user
     */
    private ?string $firstname = null;

    /**
     * @var string|null the last name of the user
     */
    private ?string $lastname = null;

    /**
     * @var string|null the password of the user
     */
    private ?string $password = null;

    /**
     * @var string|null the email of the user
     */
    private ?string $email = null;

    /**
     * @var UserRole|null the role of the user
     */
    private ?UserRole $role = null;

    /**
     * @var int|null the number of login attempts of the user
     */
    private int $attempts = 0;

    /**
     * @var DateTime|null the last login attempt of the user
     */
    private ?DateTime $lastAttempt = null;

    /**
     * Gets the value of username.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Sets the value of username.
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Gets the value of firstName.
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Sets the value of firstName.
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Gets the value of lastName.
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Sets the value of lastName.
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Gets the value of password.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets the value of password.
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets the value of email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the value of email.
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
        } elseif (true === is_string($lastAttempt)) {
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

    /**
     * @return string The user's name
     */
    public function __toString()
    {
        return $this->username;
    }
}
