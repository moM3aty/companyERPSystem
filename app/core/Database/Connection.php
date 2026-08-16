<?php
// Path: app/Core/Database/Connection.php

declare(strict_types=1);

namespace App\Core\Database;

use PDO;
use PDOException;
use PDOStatement;
use App\Core\Exceptions\DatabaseException;

/**
 * Enterprise Database Connection Wrapper
 * Manages PDO instances, nested transactions (Savepoints), and statement execution.
 */
class Connection
{
    /**
     * The active PDO connection instance.
     *
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * The active transaction level (for nested transactions).
     *
     * @var int
     */
    protected int $transactions = 0;

    /**
     * Create a new database connection instance.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get the underlying PDO instance.
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param string $query
     * @param array $bindings
     * @return bool
     * @throws DatabaseException
     */
    public function statement(string $query, array $bindings = []): bool
    {
        try {
            $statement = $this->pdo->prepare($query);
            return $statement->execute($bindings);
        } catch (PDOException $e) {
            $exception = new DatabaseException($e->getMessage(), $bindings, $e);
            throw $exception->setQuery($query);
        }
    }

    /**
     * Run a select statement and return all results.
     *
     * @param string $query
     * @param array $bindings
     * @param int $fetchMode
     * @return array
     * @throws DatabaseException
     */
    public function select(string $query, array $bindings = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($bindings);
            return $statement->fetchAll($fetchMode);
        } catch (PDOException $e) {
            $exception = new DatabaseException($e->getMessage(), $bindings, $e);
            throw $exception->setQuery($query);
        }
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param string $query
     * @param array $bindings
     * @param int $fetchMode
     * @return mixed
     * @throws DatabaseException
     */
    public function selectOne(string $query, array $bindings = [], int $fetchMode = PDO::FETCH_ASSOC): mixed
    {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($bindings);
            $result = $statement->fetch($fetchMode);
            return $result === false ? null : $result;
        } catch (PDOException $e) {
            $exception = new DatabaseException($e->getMessage(), $bindings, $e);
            throw $exception->setQuery($query);
        }
    }

    /**
     * Run an insert statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @return bool
     * @throws DatabaseException
     */
    public function insert(string $query, array $bindings = []): bool
    {
        return $this->statement($query, $bindings);
    }

    /**
     * Get the ID of the last inserted row or sequence value.
     *
     * @param string|null $name
     * @return string
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * Run an update statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @return int The number of affected rows.
     * @throws DatabaseException
     */
    public function update(string $query, array $bindings = []): int
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @return int The number of affected rows.
     * @throws DatabaseException
     */
    public function delete(string $query, array $bindings = []): int
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param string $query
     * @param array $bindings
     * @return int
     * @throws DatabaseException
     */
    protected function affectingStatement(string $query, array $bindings = []): int
    {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($bindings);
            return $statement->rowCount();
        } catch (PDOException $e) {
            $exception = new DatabaseException($e->getMessage(), $bindings, $e);
            throw $exception->setQuery($query);
        }
    }

    /**
     * Start a new database transaction.
     * Supports nested transactions using savepoints.
     *
     * @return void
     * @throws DatabaseException
     */
    public function beginTransaction(): void
    {
        try {
            if ($this->transactions === 0) {
                $this->pdo->beginTransaction();
            } else {
                $this->pdo->exec("SAVEPOINT trans_{$this->transactions}");
            }
            $this->transactions++;
        } catch (PDOException $e) {
            throw new DatabaseException("Failed to begin transaction: " . $e->getMessage(), [], $e);
        }
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     * @throws DatabaseException
     */
    public function commit(): void
    {
        try {
            if ($this->transactions === 1) {
                $this->pdo->commit();
            }
            
            $this->transactions = max(0, $this->transactions - 1);
        } catch (PDOException $e) {
            throw new DatabaseException("Failed to commit transaction: " . $e->getMessage(), [], $e);
        }
    }

    /**
     * Rollback the active database transaction.
     *
     * @return void
     * @throws DatabaseException
     */
    public function rollBack(): void
    {
        try {
            if ($this->transactions === 1) {
                $this->pdo->rollBack();
            } elseif ($this->transactions > 1) {
                $level = $this->transactions - 1;
                $this->pdo->exec("ROLLBACK TO SAVEPOINT trans_{$level}");
            }
            
            $this->transactions = max(0, $this->transactions - 1);
        } catch (PDOException $e) {
            throw new DatabaseException("Failed to rollback transaction: " . $e->getMessage(), [], $e);
        }
    }

    /**
     * Get the current transaction level.
     *
     * @return int
     */
    public function getTransactionLevel(): int
    {
        return $this->transactions;
    }
}