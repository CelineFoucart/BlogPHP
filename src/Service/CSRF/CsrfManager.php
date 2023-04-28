<?php

namespace App\Service\CSRF;

use App\Service\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * CsrfManager
 * 
 * CsrfManager generates a CSRF token and valids it.
 * 
 * Create a token:
 * ```php
 * $csrf = new CsrfManager($session);
 * $token = $csrf->generateToken();
 * ```
 * 
 * Check the token after the submission of the form:
 * ```php
 * $csrf = new CsrfManager($session);
 * $csrf->process($request);
 * ```
 * A token CSRF input:
 * ```html
 * <input type="hidden" name="_csrf" value="{{ token }}">
 * ```
 */
class CsrfManager
{
    private string           $formKey;
    private string           $sessionKey;
    private SessionInterface $session;
    private int              $limit;

    public function __construct(SessionInterface $session, int $limit = 50, string $formKey = '_csrf', string $sessionKey = 'csrf')
    {
        $this->session = $session;
        $this->formKey = $formKey;
        $this->limit = $limit;
        $this->sessionKey = $sessionKey;
    }

    /**
     * Check if the user has submitted a valid CSRF token.
     *
     * @throws CsrfInvalidException
     */
    public function process(ServerRequestInterface $request): bool
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session->get($this->sessionKey) ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);

                    return true;
                } else {
                    $this->reject();
                }
            }
        }

        return true;
    }

    /**
     * Generate a csrf token.
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session->get($this->sessionKey) ?? [];
        $csrfList[] = $token;
        $this->session->set($this->sessionKey, $csrfList);
        $this->limitTokens();

        return $token;
    }

    /**
     * Get the value of formKey.
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }

    /**
     * Limit the number of tokens.
     */
    private function limitTokens(): void
    {
        $tokens = $this->session->get($this->sessionKey) ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session->set($this->sessionKey, $tokens);
    }

    /**
     * Use a token and unset it in the array.
     */
    private function useToken(string $token): void
    {
        $tokens = array_filter($this->session->get($this->sessionKey), function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session->set($this->sessionKey, $tokens);
    }

    /**
     * Reject the csrf.
     */
    private function reject(): void
    {
        throw new CsrfInvalidException('Token CSRF invalide');
    }
}
