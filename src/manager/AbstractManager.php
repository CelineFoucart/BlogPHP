<?php

namespace App\manager;

use App\database\Database\Database;
use App\database\QueryBuilder;
use App\database\StatementBuilder;

class AbstractManager
{
    protected \PDO $pdo;
    protected string $class;
    protected string $table;

    public function __construct(string $class)
    {
        $this->pdo = Database::getPDO();
        $this->class = $class;
        $parts = explode('\\', $class);
        $this->table = strtolower(end($parts));
    }

    /**
     * Return the result of a prepared request or null.
     *
     * @return mixed
     */
    public function findBy(string $column, mixed $value)
    {
        $sql = $this->getQuery()->where($this->table[0].'.'.$column.' = ?')->toSQL();

        return $this->getBuilder()->fetch($sql, [$value]);
    }

    /**
     * Return the result as an array of entities.
     */
    public function findAll(): array
    {
        $sql = $this->getQuery()->toSQL();

        return $this->getBuilder()->fetchAll($sql);
    }

    /**
     * Count entities.
     */
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
