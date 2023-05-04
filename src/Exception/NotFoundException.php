<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Throwable;

/**
 * NotFoundException is used in case of 404 Not Found.
 */
final class NotFoundException extends Exception
{
    public function __construct(string $message = '', Throwable $previous = null)
    {
        if ('' === $message) {
            $message = "Cette page n'existe pas.";
        }
        parent::__construct($message, 404, $previous);
    }
}
