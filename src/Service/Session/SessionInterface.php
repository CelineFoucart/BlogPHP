<?php

namespace App\Service\Session;

interface SessionInterface
{
    /**
     * Start a session.
     */
    public function start(): self;

    /**
     * Get an information in session.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Add an information in session.
     */
    public function set(string $key, mixed $value): self;

    /**
     * Check if a key exists in session.
     */
    public function exists(string $key): bool;

    /**
     * Delete a key in session.
     */
    public function delete(string $key): void;

    /**
     * Destroy the session.
     */
    public function end(): self;
}
