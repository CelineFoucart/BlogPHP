<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\TwigFunction;
use App\Service\Session\Auth;
use Twig\Extension\AbstractExtension;
use App\Service\Session\SessionInterface;


class UserExtension extends AbstractExtension
{
    public function __construct(private SessionInterface $session, private Auth $auth)
    {
        
    }
    public function getFunctions()
    {
        return [
            new TwigFunction('is_logged', [$this, 'isLogged']),
            new TwigFunction('is_admin', [$this, 'isAdmin']),
        ];
    }

    public function isLogged():bool
    {
        return $this->auth->logged();
    }

    public function isAdmin(): bool
    {
        return $this->auth->isAdmin();
    }
}