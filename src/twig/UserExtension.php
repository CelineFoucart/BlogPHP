<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;


class UserExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('has_role', [$this, 'hasRole'], ['is_safe' => ['html']]),
        ];
    }

    public function hasRole(string $role):bool
    {
        return false;
    }
}