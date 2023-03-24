<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogUser;
use App\manager\BlogUserManager;
use App\manager\RoleManager;
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
                    $this->auth->session($user->getId(), $isAdmin);

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

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $errors = $this->validateForRegistration($data);

            if (empty($errors)) {
                $password = password_hash($data['password'], PASSWORD_DEFAULT);
                $roleManager = $this->getManager(RoleManager::class);
                $role = $roleManager->findBy('alias', 'ROLE_USER');

                $user = (new BlogUser())
                    ->setUsername(htmlspecialchars($data['pseudo']))
                    ->setEmail(htmlspecialchars($data['email']))
                    ->setPassword($password)
                ;

                /** @var BlogUserManager */
                $userManager = $this->getManager(BlogUserManager::class);
                $userId = $userManager->createUser($user, $role->getId());
                $this->auth->session($userId, 0);

                return $this->redirect('app_profile');
            }
        }

        return $this->render('user/register.html.twig', ['errors' => $errors]);
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

    private function validateForRegistration(array $data): array
    {
        $validator = new Validator($data);
        $validator->exist("pseudo", "email", "password");
        $validator->checkLength("pseudo", 180);
        $validator->checkMail("email");
        $validator->checkPassword("password");
        if ($validator->exist('password-confirm')) {
            $validator->equal("password", $data['password-confirm']);
        }

        return $validator->getErrors();
    }
}