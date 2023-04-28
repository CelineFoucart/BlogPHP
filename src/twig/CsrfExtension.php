<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\CSRF\CsrfManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{
    public function __construct(
        private CsrfManager $csrfManager
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('csrf_token', [$this, 'getToken']),
        ];
    }

    public function getToken(): string
    {
        return $this->csrfManager->generateToken();
    }
}