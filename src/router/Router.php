<?php

namespace App\router;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

final class Router
{
    private array $routes = [];

    private array $namedRoutes = [];

    /**
     * add a path in $routes.
     *
     * @param mixed $path
     * @param mixed $callable
     * @param mixed $name
     * @param mixed $method
     *
     * @return void
     */
    private function add(string $path, $callable, ?string $name = null, string $method, array $params = [])
    {
        $route = new Route($path, $callable);
        foreach ($params as $key => $value) {
            $route->with($key, $value);
        }

        $this->routes[$method][] = $route;
        if (is_string($callable) && null === $name) {
            $name = $callable;
        }
        if (null !== $name) {
            $this->namedRoutes[$name] = $route;
        }

        return $route;
    }

    /**
     * Create a new GET route.
     *
     * @param mixed $path     the path
     * @param mixed $callable a callable or a controller and method to call as Post#index
     * @param mixed $name     the route name
     */
    public function get(string $path, $callable, array $params = [], ?string $name = null): self
    {
        $this->add($path, $callable, $name, 'GET', $params);

        return $this;
    }

    /**
     * Create a new POST route.
     *
     * @param mixed $path     the path
     * @param mixed $callable a callable or a controller and method to call as Post#index
     * @param mixed $name     the route name
     */
    public function post(string $path, $callable, array $params = [], ?string $name = null): self
    {
        $this->add($path, $callable, $name, 'POST', $params);

        return $this;
    }

    /**
     * Create a new POST and GET route.
     *
     * @param mixed $path
     * @param mixed $callable
     * @param mixed $name
     */
    public function mixed(string $path, $callable, array $params = [], ?string $name = null): self
    {
        $this->add($path, $callable, $name, 'GET', $params);
        $this->add($path, $callable, $name, 'POST', $params);

        return $this;
    }

    /**
     * Generate a route url.
     *
     * @param mixed $name
     * @param mixed $param
     */
    public function url(string $name, array $param = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException("$name is not a named route");
        }

        return $this->namedRoutes[$name]->getURL($param);
    }

    /**
     * Call the route.
     */
    public function run(RequestInterface $request): Response
    {
        $method = $request->getMethod();
        if (!isset($this->routes[$method])) {
            throw new RouterException("$method does not exist");
        }

        $path = $request->getUri()->getPath();
        foreach ($this->routes[$method] as $route) {
            if ($route->match($path)) {
                return $route->call($this, $request);
            }
        }
        throw new RouterException('This page does not exist!');
    }
}
