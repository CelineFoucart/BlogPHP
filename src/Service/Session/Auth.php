<?php

declare(strict_types=1);

namespace App\Service\Session;

/**
 * Auth handles the session user, creating it, destroying it and retrieves the data of the user in session.
 */
class Auth
{
    protected Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Hydrates the session.
     */
    public function session(int $id, int $isAdmin, string $username): self
    {
        $this->session->set('id', $id)->set('isAdmin', $isAdmin)->set('username', $username);

        return $this;
    }

    /**
     * Checks if a user is logged.
     */
    public function logged(): bool
    {
        return $this->session->exists('id');
    }

    /**
     * Checks if a user is admin.
     */
    public function isAdmin(): bool
    {
        if (!$this->session->exists('isAdmin')) {
            return false;
        }

        return 1 === $this->session->get('isAdmin');
    }

    /**
     * Logs out the user.
     */
    public function logout(): self
    {
        $this->session->end();

        return $this;
    }

    /**
     * Returns user id.
     */
    public function getUserId(): ?int
    {
        if ($this->logged()) {
            return $this->session->get('id');
        }

        return null;
    }

    /**
     * Returns the username.
     */
    public function getUsername(): ?string
    {
        if ($this->logged()) {
            return $this->session->get('username');
        }

        return null;
    }
}
