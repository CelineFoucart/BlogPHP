<?php

declare(strict_types=1);

namespace App\Controller;

use Twig\Environment;
use App\router\Router;
use App\Twig\PathExtension;
use App\Twig\UserExtension;
use App\Service\Session\Auth;
use App\Twig\BbcodeExtension;
use GuzzleHttp\Psr7\Response;
use App\Service\Session\Session;
use App\Manager\ManagerInterface;
use App\Twig\PaginationExtension;
use Twig\Loader\FilesystemLoader;
use App\Exception\NotFoundException;
use App\exception\ForbiddenException;
use App\Exception\BadRequestException;
use App\Twig\StringExtension;

abstract class AbstractController
{
    protected Router $router;

    protected Session $session;

    protected Auth $auth;

    private array $twigVariables;

    private Environment $twig;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->session = new Session();
        $this->session->start();
        $this->auth = new Auth($this->session);

        $this->twigVariables = require dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'twig.php';
        $loader = new FilesystemLoader(PATH.DIRECTORY_SEPARATOR.'templates');
        $this->twig = new Environment($loader);
        $this->twig->addExtension(new PathExtension($this->router));
        $this->twig->addExtension(new PaginationExtension());
        $this->twig->addExtension(new UserExtension($this->session, $this->auth));
        $this->twig->addExtension(new BbcodeExtension());
        $this->twig->addExtension(new StringExtension());
    }

    /**
     * Return the template in a response.
     */
    protected function render(string $template, array $params = [], int $statusCode = 200): Response
    {
        $params = array_merge(['router' => $this->router], $this->twigVariables['twig_variables'], $params);

        return new Response($statusCode, [], $this->twig->render($template, $params));
    }

    /**
     * Returns a Manager object.
     */
    protected function getManager(string $classManager, ?string $entityClass = null, ?string $table = null): ManagerInterface
    {
        if (!$entityClass) {
            $parts = explode('\\', $classManager);
            $class = $parts[count($parts) - 1];
            $managerNameParts = explode('Manager', $class);
            $entityClass = $managerNameParts[0];
        }

        if (!$table) {
            $tableNameParts = preg_split('/(?=[A-Z])/', $entityClass);
            $table = join('_', $tableNameParts);
            $table = strtolower(trim($table, '_'));
        }

        return new $classManager('\\App\\Entity\\'.$entityClass, $table);
    }

    /**
     * Create a redirection.
     */
    protected function redirect(string $route, array $params = [], int $code = 301): Response
    {
        $url = $this->router->url($route, $params);

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
