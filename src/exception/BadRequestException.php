<?php

namespace App\Exception;

use Exception;
use Throwable;

final class BadRequestException extends Exception
{
    public function __construct(string $url, string $message = '', int $code = 0, Throwable $previous = null)
    {
        if ('' === $message) {
            $message = 'Les données soumises sont invalides.';
        }
        parent::__construct($message, 400, $previous);
    }
}
