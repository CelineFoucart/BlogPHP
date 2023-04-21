<?php

namespace App\Database\Statistics;

use App\Database\Database;
use App\Database\StatementBuilder;

class StatisticsHandler 
{
    /**
     * @var StatisticsEntity[]
     */
    private array $entities = [];

    private StatementBuilder $builder;

    public function __construct()
    { 
        $this->builder = new StatementBuilder(null, Database::getPDO());
    }

    public function addEntity(StatisticsEntity $entity): self
    {
        $this->entities[] = $entity;

        return $this;
    }

    public function getStatistics(): array
    {
        try {
            $data = $this->builder->fetchAll($this->formatQuery(), [], 'assoc');
            return $this->formatStats($data);
        } catch (\Exception $th) {
            return [];
        }
    }


    private function formatQuery(): string
    {
        $sql = [];

        foreach ($this->entities as $entity) {
            $sql[] = $entity->getQuery();
        }

        return join(" UNION ", $sql);
    }

    private function formatStats($stats): array
    {
        if(empty($stats)) {
            return [];
        }

        $data = [];

        foreach ($stats as $value) {
            $data[$value['element']] = $value['counts'];
        }

        return $data;
    }
}