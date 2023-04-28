<?php

declare(strict_types=1);

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Entity\BlogPost;
use App\Entity\BlogUser;
use App\Service\Validator;
use App\Service\Pagination;
use App\manager\CommentManager;
use App\Manager\BlogPostManager;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use App\Service\CSRF\CsrfInvalidException;

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
        $errorMessage = null;
        $commentSubmitted = "";
        $successMessage = null;
        $invalidCSRFMessage = null;

        if (null === $blogPost) {
            $this->createNotFoundException("Cet article n'existe pas.");
        }

        $commentPagination = $this->getPostComments($request, $blogPost->getId());

        try {
            if ($request->getMethod() === 'POST') {
                $this->csrf->process($request);
                $userId = $this->auth->getUserId();
                $data = $request->getParsedBody();
    
                $status = $this->createComment($data, $blogPost, $userId);
    
                if ($status['commentId'] > 0) {
                    $successMessage = $status['message'];
                }  else {
                    $errorMessage = $status['message'];
                    $commentSubmitted = (isset($data['content'])) ? htmlspecialchars($data['content']) : "";
                }
            }
        } catch (CsrfInvalidException $th) {
            $invalidCSRFMessage = $th->getMessage();
        }

        return $this->render('article/show.html.twig', [
            'post' => $blogPost,
            'commentPagination' => $commentPagination,
            'errorMessage' => $errorMessage,
            'commentSubmitted' => $commentSubmitted,
            'successMessage' => $successMessage,
            'invalidCSRFMessage' => $invalidCSRFMessage,
        ]);
    }

    /**
     * Create a comment after or returns errors.
     *
     * @param array         $data
     * @param BlogPost      $post
     * @param integer|null  $userId
     * 
     * @return array     an array with the commentId (0 if fail) and a message
     */
    private function createComment(array $data, BlogPost $post, ?int $userId): array
    {
        if (!$userId) {
            return [
                'commentId' => 0,
                'message' => "Vous devez vous connecter pour poster un commentaire."
            ];
        }

        $errors = (new Validator($data))->checkLength('content', 3, 10000)->getErrors();

        if (empty($errors)) {
            $comment = (new Comment())
                ->setCreatedAt(new DateTime())
                ->setContent($data['content'])
                ->setIsValidated(false)
                ->setUpdatedAt(new DateTime())
                ->setPost($post)
            ;

            $commentManager = $this->getCommentManager();
            $commentId = $commentManager->create($comment, $userId);

            return [
                'commentId' => $commentId,
                'message' => "Le commentaire a été enregistré et est en attente de validation.",
            ];

        } else {
            return ['commentId' => 0, 'message' => join('<br>', $errors['content'])];
        }
    }

    /**
     * Get a pagination of post comments.
     */
    private function getPostComments(ServerRequest $request, int $postId): Pagination
    {
        $commentManager = $this->getCommentManager();
        $link = (string) $request->getUri();
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $commentManager->findPaginated($postId, $link, $page);
    }

    private function getPostManager(): BlogPostManager
    {
        return $this->getManager(BlogPostManager::class);
    }

    private function getCommentManager(): CommentManager
    {
        return $this->getManager(CommentManager::class);
    }
}
