<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\router\Router;
use App\Entity\BlogPost;
use App\Service\Validator;
use App\manager\BlogPostManager;
use App\Service\Form\FormBuilder;
use GuzzleHttp\Psr7\ServerRequest;
use App\Controller\AbstractController;
use App\Entity\BlogUser;
use DateTime;
use Psr\Http\Message\ResponseInterface;

class AdminPostController  extends AbstractController
{
    private BlogPostManager $postManager;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->postManager = $this->getManager(BlogPostManager::class);
        
        if (!$this->auth->isAdmin()) {
            $this->createForbidderException("Vous ne pouvez pas consulter cette page.");
        }
    }

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

    public function show(ServerRequest $request): ResponseInterface
    {
        return $this->render('admin/post/show.html.twig', [
            'post' => $this->getPost($request),
            'activeArticle' => true,
        ]);
    }

    public function edit(ServerRequest $request): ResponseInterface
    {
        $blogPost = $this->getPost($request);
        $data = [
            'title' => $blogPost->getTitle(),
            'slug' => $blogPost->getSlug(),
            'content' => $blogPost->getContent(), 
            'description' => $blogPost->getDescription(),
        ];
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $errors = $this->validateForm($data);

            if (empty($errors)) {
                $blogPost->setTitle($data['title'])->setSlug($data['slug'])->setContent($data['content'])->setDescription($data['description']);
                $blogPost->setUpdatedAt(new DateTime());

                $this->postManager->update($blogPost);

                return $this->redirect('app_admin_post_show', ['id' => $blogPost->getId()]);
            }
        }

        return $this->render('admin/post/edit.html.twig', [
            'post' => $blogPost,
            'form' => $this->getPostForm($errors, $data),
            'activeArticle' => true,
        ]);
    }

    public function create(ServerRequest $request): ResponseInterface
    {
        $data = ['title' => "",'slug' => "",'content' => "", 'description' => ""];
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $errors = $this->validateForm($data, true);

            if (empty($errors)) {
                $userId = $this->auth->getUserId();
                if (null === $userId) {
                    $this->createForbidderException("Action impossible");
                }

                $blogPost = (new BlogPost())
                    ->setTitle($data['title'])
                    ->setSlug($data['slug'])
                    ->setContent($data['content'])
                    ->setDescription($data['description'])
                    ->setCreatedAt(new DateTime())
                    ->setUpdatedAt(new DateTime())
                ;
                $postId = $this->postManager->insert($blogPost, (int)$userId);

                return $this->redirect('app_admin_post_show', ['id' => $postId]);
            }
        }

        return $this->render('admin/post/create.html.twig', [
            'form' => $this->getPostForm($errors, $data),
            'activeArticle' => true,
        ]);
    }

    public function delete(ServerRequest $request): ResponseInterface
    {
        $blogPost = $this->getPost($request);
        $this->postManager->delete($blogPost);

        return $this->redirect('app_admin_post_index');
    }

    private function getPost(ServerRequest $request): BlogPost
    {
        $id = $request->getAttribute('id');
        $blogPost = $this->postManager->findById((int)$id);

        if (null === $blogPost) {
            $this->createNotFoundException("Cet article n'existe pas.");
        }

        return $blogPost;
    }

    private function getPostForm(array $errors, array $data)
    {
        return (new FormBuilder('POST'))
            ->setErrors($errors)
            ->setData($data)
            ->addField("title", 'text', ['label' => 'Titre', 'placeholder' => "Titre de l'article"])
            ->addField("slug", 'text', ['label' => "Lien de l'article, sans accent, espaces, caractères spéciaux ni chiffres", 'placeholder' => "Lien de l'article"])
            ->addField("description", 'textarea', ['label' => "Description", 'rows' => 2])
            ->addField("content", 'textarea', ['label' => "Contenu de l'article", 'rows' => 10])
            ->setButton("Enregistrer")
            ->renderForm()
        ;
    }

    private function validateForm(array $data, bool $isforCreation = false): array
    {
        $validator = (new Validator($data))
            ->checkLength("title", 3, 150)
            ->checkLength("slug", 3, 200)
            ->checkLength("content", 3, 20000)
            ->checkLength("description", 3, 255)
            ->slug("slug")
        ;

        if ($isforCreation) {
            $validator->isUnique("slug", 'slug', $this->postManager);
        }

        return $validator->getErrors();
    }
}