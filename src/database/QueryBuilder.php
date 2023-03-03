<?php

namespace App\database;

/**
 * Class QueryBuilder generates a SQL query.
 */
class QueryBuilder
{
    /**
     * @var string the table name
     */
    private string  $from;
    /**
     * @var array the where parts
     */
    private array   $where = [];
    /**
     * @var int|null the limit
     */
    private ?int    $limit = null;
    /**
     * @var int|null the offset
     */
    private ?int    $offset = null;
    /**
     * @var array the column for the select
     */
    private array   $selectedColumns = [];
    /**
     * @var array the order options
     */
    private array   $columnsOrder = [];
    /**
     * @var array the joined tables
     */
    private array   $joins = [];
    /**
     * @var array the values part for an insert
     */
    private array   $values = [];
    /**
     * @var string the final sql query
     */
    private string  $sql;

    /**
     * Define the value of $from.
     */
    public function from(string $table, ?string $alias = null): self
    {
        $this->from = "$table";
        if (null !== $alias) {
            $this->from .= " AS $alias";
        }

        return $this;
    }

    /**
     * Define the join part.
     */
    public function join(string $table, string $condition, ?string $type = null): self
    {
        $validatedTypes = ['LEFT', 'RIGHT', 'INNER'];
        $join = '';
        if (null !== $type) {
            $type = strtoupper($type);
            if (in_array($type, $validatedTypes)) {
                $join .= "$type ";
            }
        }
        $join .= "JOIN $table ON $condition";
        $this->joins[] = $join;

        return $this;
    }

    /**
     * Define the value of $columnsOrder.
     */
    public function orderBy(string $columnOrder, ?string $direction = null): self
    {
        $validOrder = ['DESC', 'ASC'];
        $direction = strtoupper($direction);
        if (in_array($direction, $validOrder)) {
            $this->columnsOrder[] = "$columnOrder $direction";
        } else {
            $this->columnsOrder[] = "$columnOrder";
        }

        return $this;
    }

    /**
     * Define the value of $limit.
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Define the value of $offset.
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Define the condition.
     */
    public function where(string $condition): self
    {
        $this->where[] = $condition;

        return $this;
    }

    /**
     * Define the columns for the select part.
     */
    public function select(string ...$columns): self
    {
        foreach ($columns as $column) {
            if (is_array($column)) {
                foreach ($column as $value) {
                    $this->selectedColumns[] = $value;
                }
            } else {
                $this->selectedColumns[] = $column;
            }
        }

        return $this;
    }

    /**
     * Format a count request.
     */
    public function count(?string $field = null): string
    {
        if (null === $field) {
            $this->sql = 'SELECT COUNT(*)';
        } else {
            $this->sql = "SELECT COUNT({$field})";
        }
        $this->getTable();
        $this->getConditions();

        return $this->sql;
    }

    /**
     * @param string[] ...$values
     */
    public function value(string ...$values): self
    {
        foreach ($values as $key => $value) {
            $values[$key] = ':'.$value;
        }

        $this->values = array_merge($this->values, $values);

        return $this;
    }

    /**
     * Format the sql request.
     *
     * @param string $action = "select"
     *
     * @return string|null
     */
    public function toSQL(string $action = 'select')
    {
        if ('insert' === $action) {
            $this->getInsert();

            return $this->sql;
        } elseif ('update' === $action) {
            $this->getUpdate();
        } elseif ('delete' === $action) {
            $this->getDelete();
        } else {
            $this->getSelectQuery();
        }
        $this->getConditions();

        return $this->sql;
    }

    /**
     * Format the table name.
     */
    protected function getTable(): void
    {
        if (null === $this->from) {
            throw new \Exception('La propriété from ne peut être nulle');
        }
        $this->sql .= " FROM {$this->from}";
        if (!empty($this->joins)) {
            $this->sql .= ' '.join(' ', $this->joins);
        }
    }

    /**
     * Format the sql ORDER BY part.
     */
    protected function getColumnsOrder(): void
    {
        if (!empty($this->columnsOrder)) {
            $this->sql .= ' ORDER BY ';

            if (1 === count($this->columnsOrder)) {
                $this->sql .= $this->columnsOrder[0];
            } else {
                $this->sql .= implode(', ', $this->columnsOrder);
            }
        }
    }

    /**
     * Format the sql condition parts.
     */
    protected function getConditions(): void
    {
        if (!empty($this->where)) {
            $this->sql .= ' WHERE ';

            if (1 === count($this->where)) {
                $this->sql .= $this->where[0];
            } else {
                $this->sql .= '('.join(') AND (', $this->where).')';
            }
        }

        $this->getColumnsOrder();

        if (null !== $this->limit) {
            $this->sql .= " LIMIT {$this->limit}";
        }
        if (null !== $this->offset) {
            $this->sql .= " OFFSET {$this->offset}";
        }
    }

    /**
     * Format the select sql parts.
     */
    protected function getSelectQuery(): void
    {
        $this->sql = 'SELECT ';
        if (empty($this->selectedColumns)) {
            $this->sql .= '*';
        } else {
            $this->sql .= implode(', ', $this->selectedColumns);
        }
        $this->getTable();
    }

    /**
     * Format the insert sql parts.
     */
    protected function getInsert(): void
    {
        $columns = join(', ', $this->selectedColumns);
        $values = join(', ', $this->values);

        if (preg_match('/AS/', $this->from)) {
            $from = trim(explode('AS', $this->from)[0]);
        } else {
            $from = $this->from;
        }
        $this->sql = "INSERT INTO {$from}({$columns}) VALUES($values)";
    }

    /**
     * Format the update sql parts.
     */
    protected function getUpdate(): void
    {
    }

    /**
     * Format the delete sql parts.
     */
    protected function getDelete(): void
    {
        if (preg_match('/AS/', $this->from)) {
            $from = trim(explode('AS', $this->from)[0]);
        } else {
            $from = $this->from;
        }
        $this->sql = "DELETE FROM {$from}";
    }
}
