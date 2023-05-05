<?php

declare(strict_types=1);

namespace App\Manager;

use App\Database\Database;
use App\Database\QueryBuilder;
use App\Database\StatementBuilder;

/**
 * AbstractManager provides usefull methods for all managers.
 */
class AbstractManager
{
    /**
     * @var \PDO PDO is used to perform the query
     */
    protected \PDO $pdo;

    /**
     * @var string The data will be render in an instance of this class
     */
    protected string $class;

    /**
     * @var string the table name in database
     */
    protected string $table;

    /**
     * A class name is required to create an instance hydrated with the data.
     */
    public function __construct(string $class, string $table)
    {
        $this->pdo = Database::getPDO();
        $this->class = $class;
        $this->table = $table;
    }

    /**
     * Returns the result of a prepared request or null.
     *
     * @return mixed
     */
    public function findBy(string $column, mixed $value)
    {
        $sql = $this->getQuery()->where($this->table[0].'.'.$column.' = ?')->toSQL();

        return $this->getBuilder()->fetch($sql, [$value]);
    }

    /**
     * Returns the result as an array of entities.
     */
    public function findAll(): array
    {
        $sql = $this->getQuery()->toSQL();

        return $this->getBuilder()->fetchAll($sql);
    }

    /**
     * Counts entities.
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

    /**
     * Gets a new instance of a QueryBuilder, configured to create a SQL query
     * for the current manager.
     */
    protected function getQuery(): QueryBuilder
    {
        return (new QueryBuilder())->setFrom($this->table, $this->table[0]);
    }

    /**
     * Gets a new instance of StatementBuilder which performs a SQL query.
     */
    protected function getBuilder(): StatementBuilder
    {
        return new StatementBuilder($this->class, $this->pdo);
    }

    /**
     * Gets the table name.
     */
    public function getTable(): ?string
    {
        return $this->table;
    }
}
