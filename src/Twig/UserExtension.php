<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Session\Auth;
use App\Service\Session\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * UserExtension creates twig functions for test user permissions.
 */
class UserExtension extends AbstractExtension
{
    /**
     * @param Session $session to interact with the session
     * @param Auth    $auth    to interect with the user in session
     */
    public function __construct(private Session $session, private Auth $auth)
    {
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('is_logged', [$this, 'isLogged']),
            new TwigFunction('is_admin', [$this, 'isAdmin']),
        ];
    }

    /**
     * Checks if a user is logged.
     */
    public function isLogged(): bool
    {
        return $this->auth->logged();
    }

    /**
     * Checks if a user is a admin.
     */
    public function isAdmin(): bool
    {
        return $this->auth->isAdmin();
    }
}
