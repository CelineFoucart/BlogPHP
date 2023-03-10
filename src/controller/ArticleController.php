<?php

declare(strict_types=1);

namespace App\Controller;

use App\Manager\BlogPostManager;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class ArticleController extends AbstractController
{
    public function index(ServerRequest $request): ResponseInterface
    {
        $manager = $this->getPostManager();
        $blogPosts = $manager->findPaginated();

        return $this->render('article/index.html.twig', [
            'posts' => $blogPosts,
        ]);
    }
    
    public function show(ServerRequest $request): ResponseInterface
    {
        $slug = $request->getAttribute('slug');
        $manager = $this->getPostManager();

        $blogPost = $manager->findBySlug($slug);

        if (null === $blogPost) {
            $this->createNotFoundException("Cet article n'existe pas.");
        }

        return $this->render('article/show.html.twig', [
            'post' => $blogPost,
        ]);
    }

    public function category(ServerRequest $request): ResponseInterface
    {
        $slug = $request->getAttribute('slug');

        return $this->render('article/category.html.twig', [
            'posts' => [],
        ]);
    }

    private function getPostManager(): BlogPostManager
    {
        return $this->getManager(BlogPostManager::class);
    }
}