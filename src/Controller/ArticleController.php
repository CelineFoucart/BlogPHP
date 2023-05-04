<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Manager\BlogPostManager;
use App\Manager\CommentManager;
use App\Service\CSRF\CsrfInvalidException;
use App\Service\Pagination;
use App\Service\Validator;
use DateTime;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * ArticleController handles the blog part of the website,
 * an index page and a show pages with a comment form submission.
 */
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
        $commentSubmitted = '';
        $successMessage = null;
        $invalidCSRFMessage = null;

        if (null === $blogPost) {
            $this->createNotFoundException("Cet article n'existe pas.");
        }

        $commentPagination = $this->getPostComments($request, $blogPost->getId());

        try {
            if ('POST' === $request->getMethod()) {
                $userId = $this->getUserId();
                $this->csrf->process($request);
                $data = $request->getParsedBody();

                $status = $this->createComment($data, $blogPost, $userId);

                if ($status['commentId'] > 0) {
                    $successMessage = $status['message'];
                } else {
                    $errorMessage = $status['message'];
                    $commentSubmitted = (isset($data['content'])) ? htmlspecialchars($data['content']) : '';
                }
            }
        } catch (CsrfInvalidException $th) {
            $invalidCSRFMessage = $th->getMessage();
        } catch (\Exception $th) {
            $errorMessage = $th->getMessage();
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
     * Creates a comment after or returns errors.
     *
     * @param int|null $userId
     *
     * @return array an array with the commentId (0 if fail) and a message
     */
    private function createComment(array $data, BlogPost $post, int $userId): array
    {
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
                'message' => 'Le commentaire a été enregistré et est en attente de validation.',
            ];
        } else {
            return ['commentId' => 0, 'message' => join('<br>', $errors['content'])];
        }
    }

    /**
     * Gets a pagination of post comments.
     */
    private function getPostComments(ServerRequest $request, int $postId): Pagination
    {
        $commentManager = $this->getCommentManager();
        $link = (string) $request->getUri();
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $commentManager->findPaginated($postId, $link, $page);
    }

    /**
     * Gets the blog post manager.
     */
    private function getPostManager(): BlogPostManager
    {
        return $this->getManager(BlogPostManager::class);
    }

    /**
     * Gets the comment manager.
     */
    private function getCommentManager(): CommentManager
    {
        return $this->getManager(CommentManager::class);
    }

    /**
     * Gets the id of the current user.
     */
    private function getUserId(): int
    {
        $userId = $this->auth->getUserId();
        if (!$userId) {
            throw new \Exception('Vous devez vous connecter pour poster un commentaire.');
        }

        return $userId;
    }
}
