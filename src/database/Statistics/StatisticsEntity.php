<?php

declare(strict_types=1);

namespace App\Database\Statistics;

/**
 * StatisticsEntity represents a table to which we perform a count query.
 */
class StatisticsEntity
{
    private string $query;

    private string $table;

    public function __construct(string $table)
    {
        $this->table = $table;

        $this->query = "SELECT count(id) AS counts, '{$this->table}' AS element FROM {$this->table}";
    }

    /**
     * Gets the sql query. The query counts the id as counts and returned the table name as element.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Gets table name.
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
