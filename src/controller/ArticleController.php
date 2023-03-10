<?php

declare(strict_types=1);

namespace App\Controller;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class ArticleController extends AbstractController
{
    public function index(ServerRequest $request): ResponseInterface
    {
        return $this->render('article/index.html.twig');
    }
    
    public function show(ServerRequest $request): ResponseInterface
    {
        $slug = $request->getAttribute('slug');

        return $this->render('article/show.html.twig');
    }
}