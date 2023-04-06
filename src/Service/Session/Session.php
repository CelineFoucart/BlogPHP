<?php

namespace App\Service\Session;

class Session implements SessionInterface
{
    public function start(): self
    {
        if (PHP_SESSION_NONE === session_status()) {
            session_start();
        }

        return $this;
    }

    public function end(): self
    {
        $this->start();
        unset($_SESSION);
        session_destroy();

        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    public function set(string $key, mixed $value): self
    {
        $this->start();
        $_SESSION[$key] = $value;

        return $this;
    }

    public function exists(string $key): bool
    {
        $this->start();

        return isset($_SESSION[$key]);
    }

    public function delete(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }
}
