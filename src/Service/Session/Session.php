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
     * @param string $key     the key to retrieve in session
     * @param mixed  $default the default value to return if the key is not found
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        if (true === array_key_exists($key, $_SESSION)) {
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
