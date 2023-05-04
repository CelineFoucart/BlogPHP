<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CSRF\CsrfInvalidException;
use App\Service\Form\FormBuilder;
use App\Service\Mailer;
use App\Service\Validator;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * HomeController handles the home page and the privacy page.
 */
class HomeController extends AbstractController
{
    /**
     * Renders the home page of the website with a contact form.
     */
    public function index(ServerRequest $request): ResponseInterface
    {
        $data = [];
        $errors = [];
        $successMessage = null;
        $errorMessage = null;
        $invalidCSRFMessage = null;

        try {
            if ('POST' === $request->getMethod()) {
                $this->csrf->process($request);
                $data = $request->getParsedBody();
                $errors = $this->validateContactForm($data);

                if (!isset($data['agreeTerms'])) {
                    $errors['agreeTerms'] = ['Vous devez accepter la conservation de vos données pour que votre demande soit traitée.'];
                }

                if (empty($errors)) {
                    $mailer = (new Mailer())
                        ->setTo(CONTACT_EMAIL)
                        ->setFrom($data['email'], $data['firstname'].' '.$data['lastname'])
                        ->setBody($this->twig->render('emails/contact.html.twig', ['email' => $data]))
                        ->setSubject('Demande de contact')
                    ;
                    $status = $mailer->send();

                    if ($status) {
                        $data = [];
                        $successMessage = "Votre message a été envoyé à l'administrateur.";
                    } else {
                        $errorMessage = "Il y a eu une erreur et l'envoi de l'email a échoué";
                    }
                } else {
                    $errorMessage = 'Les champs du formulaire sont mal remplis.';
                }
            }
        } catch (CsrfInvalidException $th) {
            $invalidCSRFMessage = $th->getMessage();
        }

        return $this->render('home/index.html.twig', [
            'form' => $this->getContactForm($data, $errors),
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'invalidCSRFMessage' => $invalidCSRFMessage,
        ]);
    }

    /**
     * Renders the privacy page.
     */
    public function privacy(ServerRequest $request): ResponseInterface
    {
        $serverParams = $request->getServerParams();

        return $this->render('home/privacy.html.twig', [
            'domaine' => $serverParams['REQUEST_SCHEME'].'://'.$serverParams['HTTP_HOST'],
        ]);
    }

    /**
     * Gets contact form parts.
     */
    private function getContactForm(array $data, array $errors = []): array
    {
        $token = $this->csrf->generateToken();

        return (new FormBuilder('POST'))
            ->setErrors($errors)
            ->setData($data)
            ->addField('firstname', 'text', ['label' => 'Prénom', 'placeholder' => 'Votre prénom'])
            ->addField('lastname', 'text', ['label' => 'Nom', 'placeholder' => 'Votre prénom'])
            ->addField('email', 'email', ['label' => 'Mail', 'placeholder' => 'Votre email'])
            ->addField('message', 'textarea', ['label' => 'Message', 'placeholder' => 'Votre message...', 'rows' => 5])
            ->addField('agreeTerms', 'checkbox', [
                'label' => 'J’autorise ce site à conserver mes données transmises via ce formulaire. Voir notre <a href="/privacy">Politique de confidentialité</a>.',
            ])->setButton('Soumettre', 'button')
            ->getFormParts($token)
        ;
    }

    /**
     * Validates the contact form.
     */
    private function validateContactForm(array $data): array
    {
        return (new Validator($data))
            ->checkLength('firstname', 1, 255)
            ->checkLength('lastname', 1, 255)
            ->checkMail('email')
            ->checkLength('message', 3, 10000)
            ->getErrors()
        ;
    }
}
