<?php

declare(strict_types=1);

namespace App\Database\Statistics;

/**
 * StatisticsEntity represents a table to which we perform a count query.
 */
class StatisticsEntity
{
    /**
     * @var string the query to perform
     */
    private string $query;

    /**
     * @var string the table to count
     */
    private string $table;

    /**
     * @param string $table the name of the table we need to count
     */
    public function __construct(string $table)
    {
        $this->table = $table;

        $this->query = "SELECT count(id) AS counts, '{$this->table}' AS element FROM {$this->table}";
    }

    /**
     * Gets the sql query. The query counts the id as counts and returned the table name as element.
     * 
     * @return string the SQL query
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
