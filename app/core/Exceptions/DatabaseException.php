<?php
// Path: app/Core/Exceptions/DatabaseException.php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Exception;
use Throwable;

/**
 * Enterprise Database Exception
 * Handles and wraps PDO exceptions securely to prevent sensitive data leakage.
 */
class DatabaseException extends Exception
{
    /**
     * The SQL query that caused the exception (if applicable).
     *
     * @var string|null
     */
    protected ?string $query = null;

    /**
     * The bindings used in the SQL query.
     *
     * @var array
     */
    protected array $bindings = [];

    /**
     * DatabaseException constructor.
     *
     * @param string $message The exception message.
     * @param array $bindings The query bindings.
     * @param Throwable|null $previous The previous exception (usually PDOException).
     */
    public function __construct(string $message, array $bindings = [], ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        
        $this->bindings = $bindings;
        
        // If a PDOException is passed, we can extract its code safely.
        if ($previous instanceof \PDOException) {
            $this->code = $previous->getCode();
        }
    }

    /**
     * Set the SQL query that caused the exception.
     *
     * @param string $query
     * @return self
     */
    public function setQuery(string $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Get the SQL query that caused the exception.
     *
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * Get the query bindings.
     *
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
}