<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\CSRF\CsrfManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * CsrfExtension creates a twig function which generate an CSRF token.
 */
class CsrfExtension extends AbstractExtension
{
    /**
     * @param CsrfManager $csrfManager The CSRF handler
     */
    public function __construct(
        private CsrfManager $csrfManager
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('csrf_token', [$this, 'getToken']),
        ];
    }

    /**
     * Gets a CSRF token.
     */
    public function getToken(): string
    {
        return $this->csrfManager->generateToken();
    }
}
