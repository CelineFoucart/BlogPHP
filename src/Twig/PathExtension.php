<?php

declare(strict_types=1);

namespace App\Twig;

use App\router\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * PathExtension creates a twig function which generate an path.
 */
class PathExtension extends AbstractExtension
{
    public function __construct(
        private Router $router
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'getPath']),
        ];
    }

    /**
     * Get a url from a route name.
     */
    public function getPath(string $name, array $params = []): string
    {
        return $this->router->url($name, $params);
    }
}
