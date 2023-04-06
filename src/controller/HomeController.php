<?php

declare(strict_types=1);

namespace App\Controller;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
{
    public function index(ServerRequest $request): ResponseInterface
    {
        return $this->render('home/index.html.twig');
    }

    public function privacy(ServerRequest $request): ResponseInterface
    {
        return $this->render('home/privacy.html.twig');
    }
}
