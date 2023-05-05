<?php

declare(strict_types=1);

namespace App\Router;

use GuzzleHttp\Psr7\ServerRequest;

/**
 * Route represents a route definition.
 */
final class Route
{
    /**
     * Undocumented variable.
     */
    private string $path;

    /**
     * @var string|callable a callable or a controller name like App\Controller\PostController#show
     */
    private $callable;

    private $matches = [];

    private $params = [];

    public function __construct(string $path, $callable)
    {
        $this->path = trim($path, '/');
        $this->callable = $callable;
    }

    /**
     * Adds a param to the route.
     */
    public function with(string $param, string $regex): self
    {
        $this->params[$param] = str_replace('(', '(?:', $regex);

        return $this;
    }

    private function paramMatch($match)
    {
        if (isset($this->params[$match[1]])) {
            return '('.$this->params[$match[1]].')';
        }

        return '([^/]+)';
    }

    /**
     * Checks if the given url matchs one of the routes.
     */
    public function match(string $url): bool
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        if (!preg_match($regex, $url, $matches)) {
            return false;
        }
        array_shift($matches);

        $params = [];
        foreach ($this->params as $key => $value) {
            foreach ($matches as $subject) {
                if (preg_match('('.$value.')', $subject)) {
                    $params[$key] = $subject;
                }
            }
        }
        $this->matches = $params;

        return true;
    }

    /**
     * @return string|RedirectResponse
     */
    public function call(Router $router, ServerRequest $request)
    {
        if (is_string($this->callable)) {
            $params = explode('#', $this->callable);

            $controller = $params[0];
            $controller = new $controller($router);
            $methodName = $params[1];

            foreach ($this->matches as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }

            return $controller->$methodName($request);
        } else {
            return call_user_func_array($this->callable, [$request]);
        }
    }

    public function getURL(array $params = []): string
    {
        $path = $this->path;
        foreach ($params as $key => $value) {
            $path = str_replace(":$key", (string) $value, $path);
        }

        return '/'.$path;
    }
}
