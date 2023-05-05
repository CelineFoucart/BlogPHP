<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use GuzzleHttp\Psr7\Response;

/**
 * ErrorController handles the error pages.
 */
class ErrorController extends AbstractController
{
    public const NOT_FOUND = 404;
    
    private string $templateFolder = PATH.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;

    /**
     * Displays an error page.
     */
    public function displayError(Exception $exception): Response
    {
        $error = $exception->getCode();
        $error = (!is_int($error) || $error < 100) ? 500 : $error;

        return $this->renderError($exception->getMessage(), $error);
    }

    /**
     * Renders in a appropriate response the exception.
     */
    private function renderError(string $message, int $code): Response
    {
        $file = 'errors'.DIRECTORY_SEPARATOR.(string) $code.'.html.twig';
        if (!file_exists($this->templateFolder.$file)) {
            $file = 'errors'.DIRECTORY_SEPARATOR.'400.html.twig';
        }

        return $this->render($file, ['message' => $message, 'code' => $code], $code);
    }
}
