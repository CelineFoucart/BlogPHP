<?php

declare(strict_types=1);

namespace App\Controller;

use App\Manager\BlogPostManager;
use App\manager\CommentManager;
use App\Service\Pagination;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class ArticleController extends AbstractController
{
    /**
     * Displays the blog post listing.
     */
    public function index(ServerRequest $request): ResponseInterface
    {
        $manager = $this->getPostManager();
        $link = (string) $request->getUri();
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $pagination = $manager->findPaginated($link, $page);

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a post with its comments.
     */
    public function show(ServerRequest $request): ResponseInterface
    {
        $slug = $request->getAttribute('slug');
        $postManager = $this->getPostManager();
        $blogPost = $postManager->findBySlug($slug);

        if (null === $blogPost) {
            $this->createNotFoundException("Cet article n'existe pas.");
        }

        $commentPagination = $this->getPostComments($request, $blogPost->getId());

        return $this->render('article/show.html.twig', [
            'post' => $blogPost,
            'commentPagination' => $commentPagination,
        ]);
    }

    /**
     * Get a pagination of post comments.
     */
    private function getPostComments(ServerRequest $request, int $postId): Pagination
    {
        /** @var CommentManager */
        $commentManager = $this->getManager(CommentManager::class);
        $link = (string) $request->getUri();
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $commentManager->findPaginated($postId, $link, $page);
    }

    private function getPostManager(): BlogPostManager
    {
        return $this->getManager(BlogPostManager::class);
    }
}
