<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogUser;
use App\manager\BlogUserManager;
use App\manager\UserRoleManager;
use App\Service\Form\FormBuilder;
use App\Service\Validator;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class UserController extends AbstractController
{
    public function login(ServerRequest $request): ResponseInterface
    {
        if ($this->auth->logged()) {
            return $this->redirect('app_home');
        }

        $error = false;

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $user = null;

            if (isset($data['pseudo']) && isset($data['password'])) {
                /** @var BlogUserManager */
                $userManager = $this->getManager(BlogUserManager::class);
                $user = $userManager->findUserAfterLogin(htmlspecialchars($data['pseudo']));
            }

            if ($user !== null) {
                if (password_verify($data['password'], $user->getPassword())) {
                    $userRole = $user->getRole();
                    $isAdmin = ($userRole !== null && $userRole->getAlias() === 'ROLE_ADMIN') ? 1 : 0;
                    $this->auth->session($user->getId(), $isAdmin, $user->getUsername());

                    return $this->redirect('app_profile');
                } else {
                    $error = true;
                }
            } else {
                $error = true;
            }
        }

        return $this->render('user/login.html.twig', ['error' => $error]);
    }

    public function logout(): ResponseInterface
    {
        $this->auth->logout();

        return $this->redirect('app_home');
    }
    
    public function register(ServerRequest $request): ResponseInterface
    {
        if ($this->auth->logged()) {
            return $this->redirect('app_home');
        }

        $errors = [];
        $data = [];

        if ($request->getMethod() === 'POST') {
            /** @var BlogUserManager */
            $userManager = $this->getManager(BlogUserManager::class);

            $data = $request->getParsedBody();
            $errors = $this->validateForRegistration($data, $userManager);

            if (empty($errors)) {
                $password = password_hash($data['password'], PASSWORD_DEFAULT);
                $roleManager = $this->getManager(UserRoleManager::class);
                $role = $roleManager->findBy('alias', 'ROLE_USER');

                $user = (new BlogUser())
                    ->setUsername(htmlspecialchars($data['pseudo']))
                    ->setEmail(htmlspecialchars($data['email']))
                    ->setPassword($password)
                ;

                
                $userId = $userManager->createUser($user, $role->getId());
                $this->auth->session($userId, 0, $user->getUsername());

                return $this->redirect('app_profile');
            }
        }

        return $this->render('user/register.html.twig', ['form' => $this->generateRegistrationForm($errors, $data)]);
    }

    public function profile(): ResponseInterface
    {
        $userId = $this->auth->getUserId();
        if (null === $userId) {
            return $this->redirect('app_login');
        }

        $userManager = $this->getManager(BlogUserManager::class);
        $user = $userManager->findBy('id', $userId);

        if (null === $user) {
            return $this->redirect('app_login');
        }

        return $this->render('user/profile.html.twig', ['user' => $user]);
    }

    private function generateRegistrationForm(array $errors, array $data): string
    {
        return (new FormBuilder('POST'))
            ->setErrors($errors)
            ->setData($data)
            ->setFormClasses("comment-form")
            ->addField("pseudo", 'text', ['label' => 'Pseudo', 'placeholder' => 'Votre pseudo'])
            ->addField("email", 'email', ['label' => 'Votre email', 'placeholder' => 'Votre email'])
            ->addField("password", 'password', ['label' => 'Mot de passe'])
            ->addField("password-confirm", 'password', ['label' => 'Confirmer le mot de passe'])
            ->setButton("S'inscrire", 'button')
            ->renderForm()
        ;
    }

    private function validateForRegistration(array $data, BlogUserManager $manager): array
    {
        $validator = (new Validator($data))
            ->checkLength("pseudo", 3, 180)
            ->checkMail("email")
            ->checkPassword("password")
            ->isUnique("pseudo", 'username', $manager)
            ->isUnique("email", 'email', $manager)
            ->equal("password-confirm", 'password', "mot de passe")
        ;

        return $validator->getErrors();
    }
}