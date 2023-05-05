<?php

declare(strict_types=1);

namespace App;

use App\Controller\ErrorController;
use App\Router\Router;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Kernel is the entry of the application and catchs all requests.
 */
final class Kernel
{
    /**
     * @var Router a router to generate and find routes
     */
    private Router $router;

    public function __construct()
    {
        $this->setRouter();
    }

    /**
     * Calls the route the user requests.
     */
    public function run(ServerRequest $request): Response
    {
        if (ENV === 'dev') {
            $this->displayDevError();

            return $this->router->run($request);
        }

        try {
            return $this->router->run($request);
        } catch (\Exception $th) {
            $errorController = new ErrorController($this->router);

            return $errorController->displayError($th);
        }
    }

    /**
     * Creates router.
     */
    private function setRouter(): self
    {
        $this->router = new Router();
        $routes = require dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'routes.php';

        foreach ($routes as $route) {
            $method = $route[0];
            $path = $route[1];
            $callable = $route[2];
            $params = $route[3];
            $namePath = isset($route[4]) ? $route[4] : $route[2];
            $this->router->$method($path, $callable, $params, $namePath);
        }

        return $this;
    }

    /**
     * Formats the exception in a pretty page for the dev environment.
     */
    private function displayDevError(): void
    {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }
}
