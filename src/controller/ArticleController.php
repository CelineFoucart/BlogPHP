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

        $link = (string) $request->getUri();
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $pagination = $manager->findPaginated($link, $page);

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
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