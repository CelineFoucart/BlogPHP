<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogUser;
use App\Manager\BlogUserManager;
use App\Manager\UserRoleManager;
use App\Service\CSRF\CsrfInvalidException;
use App\Service\Form\FormBuilder;
use App\Service\Validator;
use DateTime;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * UserController renders the user module pages.
 */
class UserController extends AbstractController
{
    /**
     * Renders the login page.
     */
    public function login(ServerRequest $request): ResponseInterface
    {
        if ($this->auth->logged()) {
            return $this->redirect('app_home');
        }

        $error = false;
        $invalidCSRFMessage = null;
        $attemptsError = false;

        try {
            if ('POST' === $request->getMethod()) {
                $this->csrf->process($request);
                $data = $request->getParsedBody();
                $user = $this->getUserFromPseudo($data);
                $success = $this->checkCredentials($user, $data);

                if (0 === $success) {
                    return $this->redirect('app_profile');
                } elseif (1 == $success) {
                    $error = true;
                } else {
                    $attemptsError = true;
                }
            }
        } catch (CsrfInvalidException $th) {
            $invalidCSRFMessage = $th->getMessage();
        }

        return $this->render('user/login.html.twig', [
            'error' => $error,
            'invalidCSRFMessage' => $invalidCSRFMessage,
            'attemptsError' => $attemptsError,
        ]);
    }

    /**
     * Logs out the current user.
     */
    public function logout(): ResponseInterface
    {
        $this->auth->logout();

        return $this->redirect('app_home');
    }

    /**
     * Renders the registration page and create a new user.
     */
    public function register(ServerRequest $request): ResponseInterface
    {
        if ($this->auth->logged()) {
            return $this->redirect('app_home');
        }

        $errors = [];
        $data = [];
        $invalidCSRFMessage = null;

        try {
            if ('POST' === $request->getMethod()) {
                $this->csrf->process($request);
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
        } catch (CsrfInvalidException $th) {
            $invalidCSRFMessage = $th->getMessage();
        }

        return $this->render('user/register.html.twig', [
            'form' => $this->generateRegistrationForm($errors, $data),
            'invalidCSRFMessage' => $invalidCSRFMessage,
        ]);
    }

    /**
     * Renders the login success page.
     */
    public function profile(): ResponseInterface
    {
        $user = $this->getUser();
        if (null === $user) {
            return $this->redirect('app_login');
        }

        return $this->render('user/profile.html.twig', ['user' => $user]);
    }

    /**
     * Creates a registration form.
     */
    private function generateRegistrationForm(array $errors, array $data): string
    {
        $token = $this->csrf->generateToken();

        return (new FormBuilder('POST'))
            ->setErrors($errors)
            ->setData($data)
            ->setFormClasses('comment-form')
            ->addField('pseudo', 'text', ['label' => 'Pseudo', 'placeholder' => 'Votre pseudo'])
            ->addField('email', 'email', ['label' => 'Votre email', 'placeholder' => 'Votre email'])
            ->addField('password', 'password', ['label' => 'Mot de passe'])
            ->addField('password-confirm', 'password', ['label' => 'Confirmer le mot de passe'])
            ->setButton("S'inscrire", 'button')
            ->renderForm($token)
        ;
    }

    /**
     * Validates the registration form.
     */
    private function validateForRegistration(array $data, BlogUserManager $manager): array
    {
        $validator = (new Validator($data))
            ->checkLength('pseudo', 3, 180)
            ->checkMail('email')
            ->checkPassword('password')
            ->isUnique('pseudo', 'username', $manager)
            ->isUnique('email', 'email', $manager)
            ->equal('password-confirm', 'password', 'mot de passe')
        ;

        return $validator->getErrors();
    }

    /**
     * Gets the user in session data from the database.
     */
    private function getUser(): ?BlogUser
    {
        $userId = $this->auth->getUserId();
        if (null === $userId) {
            return null;
        }

        $userManager = $this->getManager(BlogUserManager::class);
        $user = $userManager->findBy('id', $userId);

        return $user;
    }

    /**
     * Gets a user with the pseudo.
     */
    private function getUserFromPseudo(array $data): ?BlogUser
    {
        $user = null;

        if (isset($data['pseudo']) && isset($data['password'])) {
            /** @var BlogUserManager */
            $userManager = $this->getManager(BlogUserManager::class);
            $user = $userManager->findUserAfterLogin(htmlspecialchars($data['pseudo']));
        }

        return $user;
    }

    /**
     * Checks the user credentials and persists attempts.
     *
     * @return int 0 if success, 1 if credentials are invalid, 2 if the user has 3 or more attempts
     */
    private function checkCredentials(?BlogUser $user, array $data): int
    {
        if (!$user) {
            return 1;
        }

        $hasPasswordMatched = password_verify($data['password'], $user->getPassword());

        /** @var BlogUserManager */
        $userManager = $this->getManager(BlogUserManager::class);

        $lastAttempt = $user->getLastAttempt();
        if ($lastAttempt instanceof DateTime) {
            $now = new DateTime();
            $interval = $lastAttempt->diff($now);
            $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
        } else {
            $minutes = 100;
        }

        if ($user->getAttempts() >= 3 && $minutes < 15) {
            return 2;
        }

        if (!$hasPasswordMatched) {
            $user->setAttempts($user->getAttempts() + 1)->setLastAttempt(new DateTime());
            $userManager->updateAttemps($user);

            return 1;
        }

        $user->setAttempts(0);
        $userManager->updateAttemps($user);
        $userRole = $user->getRole();
        $isAdmin = (null !== $userRole && 'ROLE_ADMIN' === $userRole->getAlias()) ? 1 : 0;
        $this->auth->session($user->getId(), $isAdmin, $user->getUsername());

        return 0;
    }
}
