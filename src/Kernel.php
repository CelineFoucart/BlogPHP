<?php

namespace App;

use App\Controller\ErrorController;
use App\router\Router;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

final class Kernel
{
    private Router $router;

    public function __construct()
    {
        $this->setRouter();
    }

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
     * Create router.
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
     * Affiche l'exception formaté en environnement de dev.
     */
    private function displayDevError(): void
    {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }
}
