<?php

declare(strict_types=1);

namespace App\Service\Session;

/**
 * Session handles the session.
 */
class Session
{
    /**
     * Starts a new session if the session has not been started.
     */
    public function start(): self
    {
        if (PHP_SESSION_NONE === session_status()) {
            session_start();
        }

        return $this;
    }

    /**
     * Destroys the session.
     */
    public function end(): self
    {
        $this->start();
        unset($_SESSION);
        session_destroy();

        return $this;
    }

    /**
     * Gets an information in session.
     *
     * @param mixed $default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    /**
     * Sets a new information in session.
     */
    public function set(string $key, mixed $value): self
    {
        $this->start();
        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * Tests if a key exists in session.
     */
    public function exists(string $key): bool
    {
        $this->start();

        return isset($_SESSION[$key]);
    }

    /**
     * Deletes a key in session.
     */
    public function delete(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }
}
