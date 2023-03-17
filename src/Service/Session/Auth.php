<?php

namespace App\Service\Session;

use App\Service\Session\SessionInterface;

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
    public function session(int $id, int $isAdmin): self
    {
        $this->session->set('id', $id)->set('isAdmin', $isAdmin);

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
        if(!$this->session->exists('isAdmin')) {
            return false;
        }
        return $this->session->get('isAdmin') === 1;
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
}