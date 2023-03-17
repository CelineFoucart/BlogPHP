<?php

declare(strict_types=1);

namespace App\Manager;

use App\Database\Database;
use App\Database\QueryBuilder;
use App\Database\StatementBuilder;

class AbstractManager implements ManagerInterface
{
    protected \PDO $pdo;
    protected string $class;
    protected string $table;

    public function __construct(string $class, string $table)
    {
        $this->pdo = Database::getPDO();
        $this->class = $class;
        $this->table = $table;
    }
    
    public function findBy(string $column, mixed $value)
    {
        $sql = $this->getQuery()->where($this->table[0].'.'.$column.' = ?')->toSQL();

        return $this->getBuilder()->fetch($sql, [$value]);
    }
    
    public function findAll(): array
    {
        $sql = $this->getQuery()->toSQL();

        return $this->getBuilder()->fetchAll($sql);
    }
    
    public function count(?string $where = null, array $params = []): int
    {
        $count = $this->table[0].'.id';
        if (null === $where) {
            $sql = $this->getQuery()->count($count);
        } else {
            $sql = $this->getQuery()->where($where)->count($count);
        }

        return (int) $this->getBuilder()->fetch($sql, $params, 'num')[0];
    }

    protected function getQuery(): QueryBuilder
    {
        return (new QueryBuilder())->from($this->table, $this->table[0]);
    }

    protected function getBuilder(): StatementBuilder
    {
        return new StatementBuilder($this->class, $this->pdo);
    }

    public function getTable(): ?string
    {
        return $this->table;
    }
}
