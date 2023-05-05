<?php

declare(strict_types=1);

namespace App\Database\Statistics;

use App\Database\Database;
use App\Database\StatementBuilder;

/**
 * StatisticsHandler performs a sql count query with a UNION.
 */
class StatisticsHandler
{
    /**
     * @var StatisticsEntity[]
     */
    private array $entities = [];

    private StatementBuilder $builder;

    public function __construct()
    {
        $pdo = Database::getPDO();
        $this->builder = new StatementBuilder(null, $pdo);
    }

    /**
     * Adds a new table to count.
     */
    public function addEntity(StatisticsEntity $entity): self
    {
        $this->entities[] = $entity;

        return $this;
    }

    /**
     * Gets an associative array of stats array with the table name and the counts.
     */
    public function getStatistics(): array
    {
        try {
            $data = $this->builder->fetchAll($this->formatQuery(), [], 'assoc');

            return $this->formatStats($data);
        } catch (\Exception $th) {
            return [];
        }
    }

    /**
     * Formats the query.
     */
    private function formatQuery(): string
    {
        $sql = [];

        foreach ($this->entities as $entity) {
            $sql[] = $entity->getQuery();
        }

        return join(' UNION ', $sql);
    }

    /**
     * Formats the array of data: the table as key and the counts as value.
     */
    private function formatStats(array $stats): array
    {
        if (empty($stats)) {
            return [];
        }

        $data = [];

        foreach ($stats as $value) {
            $data[$value['element']] = $value['counts'];
        }

        return $data;
    }
}
