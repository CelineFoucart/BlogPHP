<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\router\Router;
use App\Entity\Comment;
use App\Service\Validator;
use App\manager\CommentManager;
use App\Service\Form\FormBuilder;
use GuzzleHttp\Psr7\ServerRequest;
use App\Controller\AbstractController;
use DateTime;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class AdminCommentController extends AbstractController
{
    private CommentManager $commentManager;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->commentManager = $this->getManager(CommentManager::class);
        
        if (!$this->auth->isAdmin()) {
            $this->createForbidderException("Vous ne pouvez pas consulter cette page.");
        }
    }
    
    /**
     * Display the listing comment page with filters.
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
     * Display the detail page.
     */
    public function show(ServerRequest $request): ResponseInterface
    {
        return $this->render('admin/comment/show.html.twig', [
            'activeComment' => true,
            'comment' => $this->getComment($request)
        ]);
    }

    /**
     * Display the edition page.
     */
    public function edit(ServerRequest $request): ResponseInterface
    {
        $comment = $this->getComment($request);
        $data = [ 'content' => $comment->getContent(), 'isValidated' => $comment->getIsValidated() ];
        $errors = [];

        if ($request->getMethod() === 'POST') {
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

        $form = (new FormBuilder('POST'))
            ->setData($data)
            ->setErrors($errors)
            ->addField('content', 'textarea', ['label' => "Contenu du commentaire", 'rows' => 5])
            ->addField('isValidated', 'checkbox', ['label' => 'Valider le commentaire', 'required' => false])
            ->setButton('Enregistrer')
            ->renderForm()
        ;

        return $this->render('admin/comment/edit.html.twig', [
            'activeComment' => true,
            'form' => $form,
            'comment' => $comment,
        ]);
    }

    /**
     * Update the comment statut to validated or not validated.
     */
    public function updateStatus(ServerRequest $request): ResponseInterface
    {
        $comment = $this->getComment($request);

        $data = $request->getParsedBody();
        $isValidated = (isset($data['isValidated'])) ? true : false;
        $comment->setIsValidated($isValidated)->setUpdatedAt(new DateTime());
        $this->commentManager->update($comment);

        if (isset($data['redirect'])) {
            return new Response(301, ['location' => $data['redirect']]);
        }

        return $this->redirect('app_admin_comment_show', ['id' => $comment->getId()]);
    }

    /**
     * Delete a comment.
     */
    public function delete(ServerRequest $request): ResponseInterface
    {
        $comment = $this->getComment($request);
        $this->commentManager->delete($comment);
        
        return $this->redirect('app_admin_comment_index');
    }

    /**
     * Return the comment by the id given in the url.
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