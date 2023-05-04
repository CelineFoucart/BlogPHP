<?php

declare(strict_types=1);

namespace App\exception;

use Exception;
use Throwable;

/**
 * ForbiddenException is used in case of 403 Forbidden.
 */
final class ForbiddenException extends Exception
{
    public function __construct(string $message = '', Throwable $previous = null)
    {
        if ('' === $message) {
            $message = "Vous n'avez pas la permission d'accéder à cette page.";
        }
        parent::__construct($message, 403, $previous);
    }
}
