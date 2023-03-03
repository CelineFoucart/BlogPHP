<?php

namespace App\Exception;

use Exception;
use Throwable;

final class NotFoundException extends Exception
{
    public function __construct(string $url, string $message = '', int $code = 0, Throwable $previous = null)
    {
        if ('' === $message) {
            $message = "Cette page n'existe pas.";
        }
        parent::__construct($message, 404, $previous);
    }
}
