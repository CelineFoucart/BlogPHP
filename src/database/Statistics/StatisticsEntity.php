<?php

namespace App\Database\Statistics;

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
     * Get the sql query.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Get table name.
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
