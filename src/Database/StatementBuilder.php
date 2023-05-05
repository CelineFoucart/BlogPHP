<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

/**
 * StatementBuilder makes a sql query to the database.
 */
class StatementBuilder
{
    /**
     * @var PDO PDO is used to perform the query
     */
    private PDO $pdo;

    /**
     * The entity name to hydrate.
     */
    private ?string $entity = null;

    /**
     * If the entity is null, the data will be returned as an associative array.
     */
    public function __construct(?string $entity, PDO $pdo)
    {
        $this->setEntity($entity);
        $this->pdo = $pdo;
    }

    /**
     * Alters data and return the last inserted id.
     */
    public function alter(string $sql, array $data): int
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Makes a request and a fetch.
     *
     * @return mixed
     */
    public function fetch(string $sql, array $params = [], string $mode = 'class')
    {
        if (empty($params)) {
            return $this->execute($sql, $mode)->fetch() ?: null;
        } else {
            return $this->prepare($sql, $params, $mode)->fetch() ?: null;
        }
    }

    /**
     * Makes a request and a fetchAll.
     *
     * @return mixed
     */
    public function fetchAll(string $sql, array $params = [], string $mode = 'class')
    {
        if (empty($params)) {
            return $this->execute($sql, $mode)->fetchAll();
        } else {
            return $this->prepare($sql, $params, $mode)->fetchAll();
        }
    }

    /**
     * Sets the value of entity, a class name.
     */
    public function setEntity(?string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Sets the value of the entity to null.
     */
    public function unsetEntity(): self
    {
        $this->entity = null;

        return $this;
    }

    /**
     * Defines the fetch mode.
     *
     * @param PDOStatement|false $query
     * @param string             $mode  the fetch mode, as array, assoc or class
     *
     * @return PDOStatement|false
     */
    private function setFetchMode($query, $mode)
    {
        if ('num' === $mode) {
            $query->setFetchMode(PDO::FETCH_NUM);
        } elseif ('assoc' === $mode || 'array' === $mode) {
            $query->setFetchMode(PDO::FETCH_ASSOC);
        } else {
            if (null === $this->entity) {
                $query->setFetchMode(PDO::FETCH_ASSOC);
            } else {
                $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
            }
        }

        return $query;
    }

    /**
     * Makes a request.
     *
     * @return PDOStatement|false
     */
    private function execute(string $sql, string $mode)
    {
        $query = $this->pdo->query($sql);

        return $this->setFetchMode($query, $mode);
    }

    /**
     * Makes a prepared request.
     *
     * @return PDOStatement|false
     */
    private function prepare(string $sql, array $params, string $mode)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $this->setFetchMode($statement, $mode);
    }
}
