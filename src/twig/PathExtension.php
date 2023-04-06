<?php

declare(strict_types=1);

namespace App\Twig;

use App\router\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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

    public function getPath(string $name, array $params = []): string
    {
        return $this->router->url($name, $params);
    }
}
