<?php

namespace App\Service\Session;

class Auth
{
    protected SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Hydrate the session.
     */
    public function session(int $id, int $isAdmin, string $username): self
    {
        $this->session->set('id', $id)->set('isAdmin', $isAdmin)->set('username', $username);

        return $this;
    }

    /**
     * Check if a user is logged.
     */
    public function logged(): bool
    {
        return $this->session->exists('id');
    }

    /**
     * Check if a user is admin.
     */
    public function isAdmin(): bool
    {
        if (!$this->session->exists('isAdmin')) {
            return false;
        }

        return 1 === $this->session->get('isAdmin');
    }

    /**
     * Logout the user.
     */
    public function logout(): self
    {
        $this->session->end();

        return $this;
    }

    /**
     * Return user id.
     */
    public function getUserId(): ?int
    {
        if ($this->logged()) {
            return $this->session->get('id');
        }

        return null;
    }

    /**
     * Return the username.
     */
    public function getUsername(): ?string
    {
        if ($this->logged()) {
            return $this->session->get('username');
        }

        return null;
    }
}
