<?php

declare(strict_types=1);

namespace App\Router;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * A small routing class.
 */
final class Router
{
    private array $routes = [];

    private array $namedRoutes = [];

    /**
     * Adds a new route.
     */
    private function add(string $path, $callable, ?string $name = null, string $method, array $params = []): Route
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
     * Creates a new GET route.
     *
     * @param string $path       the route path
     * @param mixed $callable    a callable or a controller and method to call as Post#index
     * @param array $params      the route params.
     * @param string|null $name  the route name
     * 
     * @return self
     */
    public function get(string $path, $callable, array $params = [], ?string $name = null): self
    {
        $this->add($path, $callable, $name, 'GET', $params);

        return $this;
    }

    /**
     * Creates a new POST route.
     *
     * @param string $path       the route path
     * @param mixed $callable    a callable or a controller and method to call as Post#index
     * @param array $params      the route params.
     * @param string|null $name  the route name
     * 
     * @return self
     */
    public function post(string $path, $callable, array $params = [], ?string $name = null): self
    {
        $this->add($path, $callable, $name, 'POST', $params);

        return $this;
    }

    /**
     * Creates a new POST and GET route.
     *
     * @param string $path       the route path
     * @param mixed $callable    a callable or a controller and method to call as Post#index
     * @param array $params      the route params.
     * @param string|null $name  the route name
     * 
     * @return self
     */
    public function mixed(string $path, $callable, array $params = [], ?string $name = null): self
    {
        $this->add($path, $callable, $name, 'GET', $params);
        $this->add($path, $callable, $name, 'POST', $params);

        return $this;
    }

    /**
     * Generates a route url.
     *
     * @param string $name
     * @param array  $param
     */
    public function url(string $name, array $param = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException("$name is not a named route");
        }

        return $this->namedRoutes[$name]->getURL($param);
    }

    /**
     * Calls the route.
     * 
     * @throws RouterException if the route doesn't exist.
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
