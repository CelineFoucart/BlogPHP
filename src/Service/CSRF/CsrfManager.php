<?php

declare(strict_types=1);

namespace App\Service\CSRF;

use App\Service\Session\Session;
use Psr\Http\Message\ServerRequestInterface;

/**
 * CsrfManager.
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
    /**
     * @var string the name of the CSRF input token
     */
    private string  $formKey;

    /**
     * @var string the key for CSRF tokens in the session
     */
    private string  $sessionKey;

    /**
     * @var Session retrieves the CSRF token in the session
     */
    private Session $session;

    /**
     * A limit of CSRF token in the session. The older ones are deleted.
     */
    private int     $limit;

    /**
     * @param Session $session    the session handler
     * @param int     $limit      the limit of token saved in session, by default 50
     * @param string  $formKey    the value of the name if the CSRF input, by default '_csrf'
     * @param string  $sessionKey the key in session, by default 'csrf'
     */
    public function __construct(Session $session, int $limit = 50, string $formKey = '_csrf', string $sessionKey = 'csrf')
    {
        $this->session = $session;
        $this->formKey = $formKey;
        $this->limit = $limit;
        $this->sessionKey = $sessionKey;
    }

    /**
     * Checks if the user has submitted a valid CSRF token.
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
                if (in_array($params[$this->formKey], $csrfList) === true) {
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
     * Generates a csrf token.
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
     * Gets the value of formKey.
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }

    /**
     * Limits the number of tokens.
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
     * Uses a token and unset it in the array.
     */
    private function useToken(string $token): void
    {
        $tokens = array_filter($this->session->get($this->sessionKey), function ($tokenInSession) use ($token) {
            return $token !== $tokenInSession;
        });
        $this->session->set($this->sessionKey, $tokens);
    }

    /**
     * Rejects the csrf.
     */
    private function reject(): void
    {
        throw new CsrfInvalidException('Token CSRF invalide');
    }
}
