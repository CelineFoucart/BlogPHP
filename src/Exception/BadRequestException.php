<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Throwable;

/**
 * BadRequestException is used in case of 400 Bad Request.
 */
final class BadRequestException extends Exception
{
    public function __construct(string $message = '', Throwable $previous = null)
    {
        if ('' === $message) {
            $message = 'Les données soumises sont invalides.';
        }
        parent::__construct($message, 400, $previous);
    }
}
