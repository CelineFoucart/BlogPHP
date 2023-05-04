<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Comment;
use App\Manager\CommentManager;
use App\Router\Router;
use App\Service\CSRF\CsrfInvalidException;
use App\Service\Form\FormBuilder;
use App\Service\Validator;
use DateTime;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * AdminCommentController handle the moderation pages for the comments.
 */
class AdminCommentController extends AbstractController
{
    private CommentManager $commentManager;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->commentManager = $this->getManager(CommentManager::class);

        if (!$this->auth->isAdmin()) {
            $this->createForbidderException('Vous ne pouvez pas consulter cette page.');
        }
    }

    /**
     * Displays the listing comment page with filters.
     */
    public function index(ServerRequest $request): ResponseInterface
    {
        $link = (string) $request->getUri();
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $option = isset($params['option']) ? $params['option'] : 'all';

        if (!in_array($option, ['all', 'validated', 'notValidated'])) {
            $option = 'all';
        }

        $pagination = $this->commentManager->findPaginatedWithFilter($link, $page, $option);

        return $this->render('admin/comment/index.html.twig', [
            'activeComment' => true,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays the detail page.
     */
    public function show(ServerRequest $request): ResponseInterface
    {
        return $this->render('admin/comment/show.html.twig', [
            'activeComment' => true,
            'comment' => $this->getComment($request),
        ]);
    }

    /**
     * Displays the edition page.
     */
    public function edit(ServerRequest $request): ResponseInterface
    {
        $comment = $this->getComment($request);
        $data = ['content' => $comment->getContent(), 'isValidated' => $comment->getIsValidated()];
        $errors = [];
        $invalidCSRFMessage = null;

        try {
            if ('POST' === $request->getMethod()) {
                $this->csrf->process($request);
                $data = $request->getParsedBody();
                $data['isValidated'] = (isset($data['isValidated'])) ? true : false;
                $errors = (new Validator($data))->checkLength('content', 3, 10000)->getErrors();

                if (empty($errors)) {
                    $comment
                        ->setContent(htmlspecialchars($data['content']))
                        ->setUpdatedAt(new DateTime())
                        ->setIsValidated($data['isValidated'])
                    ;
                    $this->commentManager->update($comment);

                    return $this->redirect('app_admin_comment_show', ['id' => $comment->getId()]);
                }
            }
        } catch (CsrfInvalidException $th) {
            $invalidCSRFMessage = $th->getMessage();
        }

        $form = (new FormBuilder('POST'))
            ->setData($data)
            ->setErrors($errors)
            ->addField('content', 'textarea', ['label' => 'Contenu du commentaire', 'rows' => 5])
            ->addField('isValidated', 'checkbox', ['label' => 'Valider le commentaire', 'required' => false])
            ->setButton('Enregistrer')
            ->renderForm($this->csrf->generateToken())
        ;

        return $this->render('admin/comment/edit.html.twig', [
            'activeComment' => true,
            'form' => $form,
            'comment' => $comment,
            'invalidCSRFMessage' => $invalidCSRFMessage,
        ]);
    }

    /**
     * Updates the comment statut to validated or not validated.
     */
    public function updateStatus(ServerRequest $request): ResponseInterface
    {
        try {
            $this->csrf->process($request);
            $comment = $this->getComment($request);

            $data = $request->getParsedBody();
            $isValidated = (isset($data['isValidated'])) ? true : false;
            $comment->setIsValidated($isValidated)->setUpdatedAt(new DateTime());
            $this->commentManager->update($comment);

            if (isset($data['redirect'])) {
                return new Response(301, ['location' => $data['redirect']]);
            }

            return $this->redirect('app_admin_comment_show', ['id' => $comment->getId()]);
        } catch (\Throwable $th) {
            return $this->render('admin/components/error.html.twig', [
                'errorMessage' => $th->getMessage(),
                'title' => 'Erreur lors de la validation',
            ]);
        }
    }

    /**
     * Deletes a comment.
     */
    public function delete(ServerRequest $request): ResponseInterface
    {
        try {
            $comment = $this->getComment($request);
            $this->commentManager->delete($comment);

            return $this->redirect('app_admin_comment_index');
        } catch (\Exception $th) {
            return $this->render('admin/components/error.html.twig', [
                'errorMessage' => $th->getMessage(),
                'title' => 'Erreur de suppression',
            ]);
        }
    }

    /**
     * Returns the comment by the id given in the url.
     */
    private function getComment(ServerRequest $request): Comment
    {
        $id = $request->getAttribute('id');
        $comment = $this->commentManager->findById((int) $id);

        if (null === $comment) {
            $this->createNotFoundException("Ce commentaire n'existe pas.");
        }

        return $comment;
    }
}
