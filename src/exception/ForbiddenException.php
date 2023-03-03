<?php

namespace App\exception;

use Exception;
use Throwable;

final class ForbiddenException extends Exception
{
    public function __construct(string $url, string $message = '', int $code = 0, Throwable $previous = null)
    {
        if ('' === $message) {
            $message = "Vous n'avez pas la permission d'accéder à cette page.";
        }
        parent::__construct($message, 403, $previous);
    }
}
