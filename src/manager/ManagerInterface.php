<?php

declare(strict_types=1);

namespace App\manager;

interface ManagerInterface
{
    /**
     * Return the result of a prepared request or null.
     *
     * @return mixed
     */
    public function findBy(string $column, mixed $value);

    /**
     * Return the result as an array of entities.
     */
    public function findAll(): array;

    /**
     * Count entities.
     */
    public function count(?string $where = null, array $params = []): int;

    /**
     * Get the table name.
     */
    public function getTable(): ?string;
}
