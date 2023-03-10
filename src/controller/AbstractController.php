<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\BadRequestException;
use App\exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\router\Router;
use App\Twig\PathExtension;
use GuzzleHttp\Psr7\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    protected Router $router;

    private array $twigVariables;

    private Environment $twig;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->twigVariables = require dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'twig.php';
        $loader = new FilesystemLoader(PATH.DIRECTORY_SEPARATOR.'templates');
        $this->twig = new Environment($loader);
        $this->twig->addExtension(new PathExtension($this->router));
    }

    /**
     * Return the template in a response.
     */
    public function render(string $template, array $params = [], int $statusCode = 200): Response
    {
        $params = array_merge(['router' => $this->router], $this->twigVariables['twig_variables'], $params);

        return new Response($statusCode, [], $this->twig->render($template, $params));
    }

    /**
     * Create a redirection.
     */
    protected function redirect(string $route, int $code = 301): Response
    {
        $url = $this->router->url($route);

        return new Response($code, ['location' => $url]);
    }

    /**
     * Throw a not found exception.
     *
     * @throws NotFoundException
     */
    protected function createNotFoundException(string $message = ''): void
    {
        throw new NotFoundException($message);
    }

    /**
     * Throw a not found exception.
     *
     * @throws ForbiddenException
     */
    protected function createForbidderException(string $message = ''): void
    {
        throw new ForbiddenException($message);
    }

    /**
     * Throw a not found exception.
     *
     * @throws BadRequestException
     */
    protected function createBadRequestException(string $message = ''): void
    {
        throw new BadRequestException($message);
    }
}
