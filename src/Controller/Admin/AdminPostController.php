<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\BlogPost;
use App\Entity\BlogUser;
use App\manager\BlogPostManager;
use App\manager\BlogUserManager;
use App\router\Router;
use App\Service\CSRF\CsrfInvalidException;
use App\Service\Form\FormBuilder;
use App\Service\Validator;
use DateTime;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * AdminPostController handles the blog post CRUD pages.
 */
class AdminPostController extends AbstractController
{
    private BlogPostManager $postManager;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->postManager = $this->getManager(BlogPostManager::class);

        if (!$this->auth->isAdmin()) {
            $this->createForbidderException('Vous ne pouvez pas consulter cette page.');
        }
    }

    /**
     * Displays the post listing page with pagination.
     */
    public function index(ServerRequest $request): ResponseInterface
    {
        $link = (string) $request->getUri();
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $pagination = $this->postManager->findPaginated($link, $page);

        return $this->render('admin/post/index.html.twig', [
            'pagination' => $pagination,
            'activeArticle' => true,
        ]);
    }

    /**
     * Displays the detail page.
     */
    public function show(ServerRequest $request): ResponseInterface
    {
        return $this->render('admin/post/show.html.twig', [
            'post' => $this->getPost($request),
            'activeArticle' => true,
        ]);
    }

    /**
     * Displays the edition page.
     */
    public function edit(ServerRequest $request): ResponseInterface
    {
        $blogPost = $this->getPost($request);
        $users = $this->getUsers();
        $invalidCSRFMessage = null;

        $data = [
            'title' => $blogPost->getTitle(),
            'slug' => $blogPost->getSlug(),
            'content' => $blogPost->getContent(),
            'description' => $blogPost->getDescription(),
            'author' => $blogPost->getAuthor()->getId(),
        ];
        $errors = [];

        try {
            if ('POST' === $request->getMethod()) {
                $this->csrf->process($request);
                $data = $request->getParsedBody();
                $errors = $this->validateForm($data, false, array_keys($users));

                if (empty($errors)) {
                    $blogPost
                        ->setTitle($data['title'])
                        ->setSlug($data['slug'])
                        ->setContent($data['content'])
                        ->setDescription($data['description'])
                        ->setAuthor((new BlogUser())->setId((int) $data['author']))
                        ->setUpdatedAt(new DateTime())
                    ;

                    $this->postManager->update($blogPost);

                    return $this->redirect('app_admin_post_show', ['id' => $blogPost->getId()]);
                }
            }
        } catch (CsrfInvalidException $th) {
            $invalidCSRFMessage = $th->getMessage();
        }

        return $this->render('admin/post/edit.html.twig', [
            'post' => $blogPost,
            'form' => $this->getPostForm($errors, $data, $users),
            'activeArticle' => true,
            'invalidCSRFMessage' => $invalidCSRFMessage,
        ]);
    }

    /**
     * Displays the creation page.
     */
    public function create(ServerRequest $request): ResponseInterface
    {
        $data = ['title' => '', 'slug' => '', 'content' => '', 'description' => '', 'author' => $this->auth->getUserId()];
        $errors = [];
        $users = $this->getUsers();
        $invalidCSRFMessage = null;

        try {
            if ('POST' === $request->getMethod()) {
                $this->csrf->process($request);
                $data = $request->getParsedBody();
                $errors = $this->validateForm($data, true, array_keys($users));

                if (empty($errors)) {
                    $userId = $this->auth->getUserId();
                    if (null === $userId) {
                        $this->createForbidderException('Action impossible');
                    }

                    $blogPost = (new BlogPost())
                        ->setTitle($data['title'])
                        ->setSlug($data['slug'])
                        ->setContent($data['content'])
                        ->setDescription($data['description'])
                        ->setCreatedAt(new DateTime())
                        ->setUpdatedAt(new DateTime())
                        ->setAuthor((new BlogUser())->setId((int) $data['author']))
                    ;
                    $postId = $this->postManager->insert($blogPost);

                    return $this->redirect('app_admin_post_show', ['id' => $postId]);
                }
            }
        } catch (CsrfInvalidException $th) {
            $invalidCSRFMessage = $th->getMessage();
        }

        return $this->render('admin/post/create.html.twig', [
            'form' => $this->getPostForm($errors, $data, $users),
            'activeArticle' => true,
            'invalidCSRFMessage' => $invalidCSRFMessage,
        ]);
    }

    /**
     * Deletes a post.
     */
    public function delete(ServerRequest $request): ResponseInterface
    {
        try {
            $this->csrf->process($request);
            $blogPost = $this->getPost($request);
            $this->postManager->delete($blogPost);

            return $this->redirect('app_admin_post_index');
        } catch (\Exception $th) {
            return $this->render('admin/components/error.html.twig', [
                'errorMessage' => $th->getMessage(),
                'title' => 'Erreur de suppression',
            ]);
        }
    }

    /**
     * Returns the post by the id given in the url.
     */
    private function getPost(ServerRequest $request): BlogPost
    {
        $id = $request->getAttribute('id');
        $blogPost = $this->postManager->findById((int) $id);

        if (null === $blogPost) {
            $this->createNotFoundException("Cet article n'existe pas.");
        }

        return $blogPost;
    }

    /**
     * Returns the post form.
     */
    private function getPostForm(array $errors, array $data, array $options): string
    {
        $token = $this->csrf->generateToken();

        return (new FormBuilder('POST'))
            ->setErrors($errors)
            ->setData($data)
            ->addField('title', 'text', ['label' => 'Titre', 'placeholder' => "Titre de l'article"])
            ->addField('slug', 'text', ['label' => "Lien de l'article, sans accent, espaces, caractères spéciaux ni chiffres", 'placeholder' => "Lien de l'article"])
            ->addField('description', 'textarea', ['label' => 'Description', 'rows' => 2])
            ->addField('content', 'textarea', ['label' => "Contenu de l'article", 'rows' => 10])
            ->addField('author', 'select', ['label' => 'Auteur', 'options' => $options])
            ->setButton('Enregistrer')
            ->renderForm($token)
        ;
    }

    /**
     * Validates a post form submitted.
     */
    private function validateForm(array $data, bool $isforCreation = false, array $usersIds = []): array
    {
        $validator = (new Validator($data))
            ->checkLength('title', 3, 150)
            ->checkLength('slug', 3, 200)
            ->checkLength('content', 3, 20000)
            ->checkLength('description', 3, 255)
            ->slug('slug')
        ;

        if ($isforCreation) {
            $validator->isUnique('slug', 'slug', $this->postManager);
        }

        return $validator->getErrors();
    }

    /**
     * Returns a list of blog users for the author select.
     */
    private function getUsers(): array
    {
        /** @var BlogUserManager */
        $manager = $this->getManager(BlogUserManager::class);
        $users = $manager->findUserList();
        $options = [];
        foreach ($users as $user) {
            $options[] = $user;
        }

        return $options;
    }
}