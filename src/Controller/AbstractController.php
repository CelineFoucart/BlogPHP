<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\BadRequestException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Manager\AbstractManager;
use App\Router\Router;
use App\Service\CSRF\CsrfManager;
use App\Service\Session\Auth;
use App\Service\Session\Session;
use App\Twig\BbcodeExtension;
use App\Twig\CsrfExtension;
use App\Twig\PaginationExtension;
use App\Twig\PathExtension;
use App\Twig\StringExtension;
use App\Twig\UserExtension;
use GuzzleHttp\Psr7\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * AbstractController provides dependencies required in all controller as an instance of Twig
 * and shortcuts.
 */
abstract class AbstractController
{
    /**
     * @var Router generates route url for redirections and link in templates
     */
    protected Router $router;

    /**
     * @var Session handles sessions
     */
    protected Session $session;

    /**
     * @var Auth retrieves user informations in sessions
     */
    protected Auth $auth;

    /**
     * @var array an array of global variables for twig templates
     */
    private array $twigVariables;

    /**
     * @var Environment renders templates
     */
    protected Environment $twig;

    /**
     * @var CsrfManager handles CSRF protection
     */
    protected CsrfManager $csrf;

    /**
     * A router is required to generate urls.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->session = new Session();
        $this->session->start();
        $this->auth = new Auth($this->session);
        $this->csrf = new CsrfManager($this->session);
        $this->setTwig();
    }

    /**
     * Creates a twig object and add the extensions.
     */
    private function setTwig(): self
    {
        $this->twigVariables = require dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'twig.php';
        $loader = new FilesystemLoader(PATH.DIRECTORY_SEPARATOR.'templates');
        $this->twig = new Environment($loader);
        $this->twig->addExtension(new PathExtension($this->router));
        $this->twig->addExtension(new PaginationExtension());
        $this->twig->addExtension(new UserExtension($this->session, $this->auth));
        $this->twig->addExtension(new BbcodeExtension());
        $this->twig->addExtension(new StringExtension());
        $this->twig->addExtension(new CsrfExtension($this->csrf));

        return $this;
    }

    /**
     * Returns the template in a response.
     */
    protected function render(string $template, array $params = [], int $statusCode = 200): Response
    {
        $params = array_merge(
            ['router' => $this->router, 'session_user' => ['id' => $this->auth->getUserId(), 'username' => $this->auth->getUsername()]],
            $this->twigVariables['twig_variables'], $params
        );

        return new Response($statusCode, [], $this->twig->render($template, $params));
    }

    /**
     * Returns a Manager object.
     */
    protected function getManager(string $classManager, ?string $entityClass = null, ?string $table = null): AbstractManager
    {
        if (!$entityClass) {
            $parts = explode('\\', $classManager);
            $class = $parts[count($parts) - 1];
            $managerNameParts = explode('Manager', $class);
            $entityClass = $managerNameParts[0];
        }

        if ($table === null) {
            $tableNameParts = preg_split('/(?=[A-Z])/', $entityClass);
            $table = join('_', $tableNameParts);
            $table = strtolower(trim($table, '_'));
        }

        return new $classManager('\\App\\Entity\\'.$entityClass, $table);
    }

    /**
     * Creates a redirection.
     */
    protected function redirect(string $route, array $params = [], int $code = 301): Response
    {
        $url = $this->router->url($route, $params);

        return new Response($code, ['location' => $url]);
    }

    /**
     * Throws a not found exception.
     *
     * @throws NotFoundException
     */
    protected function createNotFoundException(string $message = ''): void
    {
        throw new NotFoundException($message);
    }

    /**
     * Throws a not found exception.
     *
     * @throws ForbiddenException
     */
    protected function createForbidderException(string $message = ''): void
    {
        throw new ForbiddenException($message);
    }

    /**
     * Throws a not found exception.
     *
     * @throws BadRequestException
     */
    protected function createBadRequestException(string $message = ''): void
    {
        throw new BadRequestException($message);
    }
}
