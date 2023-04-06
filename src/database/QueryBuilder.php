<?php

namespace App\Database;

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
     * @var string|null the table name alias
     */
    private ?string  $alias = null;

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

    private array $params = [];

    /**
     * Define the value of $from.
     */
    public function from(string $table, ?string $alias = null): self
    {
        $this->from = "$table";
        if (null !== $alias) {
            $this->from .= " AS $alias";
            $this->alias = $alias;
        }

        return $this;
    }

    /**
     * Define the left join part.
     */
    public function leftJoin(string $table, string $condition): self
    {
        $this->joins[] = "LEFT JOIN $table ON $condition";

        return $this;
    }

    /**
     * Define the inner join part.
     */
    public function innerJoin(string $table, string $condition): self
    {
        $this->joins[] = "INNER JOIN $table ON $condition";

        return $this;
    }

    /**
     * Define the right join part.
     */
    public function rightJoin(string $table, string $condition): self
    {
        $this->joins[] = "RIGHT JOIN $table ON $condition";

        return $this;
    }

    /**
     * Define the join part.
     */
    public function join(string $table, string $condition): self
    {
        $this->joins[] = "JOIN $table ON $condition";

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
     * Format the sql request, for a select, an update, an insert or a delete.
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
        $keys = array_keys($this->params);
        
        $keys = array_map(function ($item) {
            return ':'.(string) $item;
        }, $keys);
        $alias = join(', ', $keys);

        if (preg_match('/AS/', $this->from)) {
            $from = trim(explode('AS', $this->from)[0]);
        } else {
            $from = $this->from;
        }
        $this->sql = "INSERT INTO {$from}({$columns}) VALUES($alias)";
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

    /**
     * Get the value of params.
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set the value of params.
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get the value of alias.
     *
     * @return ?string
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }
}
