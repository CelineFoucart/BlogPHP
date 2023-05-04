<?php

declare(strict_types=1);

namespace App\Service\CSRF;

use Exception;

/**
 * CsrfInvalidException is thrown when the CSRF token route is not valid.
 */
class CsrfInvalidException extends Exception
{
}
