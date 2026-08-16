<?php
// Path: app/Core/Database/QueryBuilder.php

declare(strict_types=1);

namespace App\Core\Database;

use App\Core\Exceptions\DatabaseException;

/**
 * Enterprise Fluent Query Builder
 * Constructs secure SQL queries using PDO Prepared Statements to prevent SQL Injection.
 */
class QueryBuilder
{
    protected Connection $connection;
    protected string $table = '';
    protected array $selects = ['*'];
    protected array $wheres = [];
    protected array $joins = [];
    protected array $bindings = [
        'select' => [],
        'join'   => [],
        'where'  => [],
        'having' => [],
        'order'  => [],
    ];
    protected array $orderBy = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    /**
     * QueryBuilder constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    
    /**
     * Set the target table.
     *
     * @param string $table
     * @return self
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Set the columns to be selected.
     *
     * @param array|string $columns
     * @return self
     */
    public function select(array|string $columns = ['*']): self
    {
        $this->selects = is_array($columns) ? $columns : func_get_args();
        return $this;
    }


    /**
     * Add a basic where clause to the query.
     *
     * @param string $column
     * @param string|null $operator
     * @param mixed $value
     * @param string $boolean
     * @return self
     */
    public function where(string $column, ?string $operator = null, mixed $value = null, string $boolean = 'AND'): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = compact('column', 'operator', 'value', 'boolean');
        $this->bindings['where'][] = $value;

        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param string $column
     * @param string|null $operator
     * @param mixed $value
     * @return self
     */
    public function orWhere(string $column, ?string $operator = null, mixed $value = null): self
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * Add a "where null" clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @return self
     */
    public function whereNull(string $column, string $boolean = 'AND'): self
    {
        $this->wheres[] = ['type' => 'Null', 'column' => $column, 'boolean' => $boolean];
        return $this;
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @return self
     */
    public function whereNotNull(string $column, string $boolean = 'AND'): self
    {
        $this->wheres[] = ['type' => 'NotNull', 'column' => $column, 'boolean' => $boolean];
        return $this;
    }


    /**
     * Add a join clause to the query.
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     * @return self
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = compact('table', 'first', 'operator', 'second', 'type');
        return $this;
    }

    /**
     * Add a left join to the query.
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return self
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }


    /**
     * Add an "order by" clause to the query.
     *
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orderBy[] = compact('column', 'direction');
        return $this;
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $value
     * @return self
     */
    public function limit(int $value): self
    {
        $this->limit = $value;
        return $this;
    }

    /**
     * Set the "offset" value of the query.
     *
     * @param int $value
     * @return self
     */
    public function offset(int $value): self
    {
        $this->offset = $value;
        return $this;
    }


    /**
     * Compile the SELECT query.
     *
     * @return string
     */
    public function toSql(): string
    {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";

        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
            }
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $whereSql = [];
            
            foreach ($this->wheres as $i => $where) {
                $boolean = $i > 0 ? " {$where['boolean']} " : "";
                
                if (isset($where['type']) && $where['type'] === 'Null') {
                    $whereSql[] = $boolean . "{$where['column']} IS NULL";
                } elseif (isset($where['type']) && $where['type'] === 'NotNull') {
                    $whereSql[] = $boolean . "{$where['column']} IS NOT NULL";
                } else {
                    $whereSql[] = $boolean . "{$where['column']} {$where['operator']} ?";
                }
            }
            $sql .= implode('', $whereSql);
        }

        if (!empty($this->orderBy)) {
            $orders = array_map(fn($order) => "{$order['column']} " . strtoupper($order['direction']), $this->orderBy);
            $sql .= " ORDER BY " . implode(', ', $orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }


    /**
     * Execute the query as a "select" statement.
     *
     * @return array
     * @throws DatabaseException
     */
    public function get(): array
    {
        return $this->connection->select($this->toSql(), $this->getBindings());
    }

    /**
     * Execute the query and get the first result.
     *
     * @return mixed
     * @throws DatabaseException
     */
    public function first(): mixed
    {
        $this->limit(1);
        return $this->connection->selectOne($this->toSql(), $this->getBindings());
    }

    /**
     * Insert a new record into the database.
     *
     * @param array $values
     * @return int The last inserted ID.
     * @throws DatabaseException
     */
    public function insert(array $values): int
    {
        $columns = implode(', ', array_keys($values));
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $this->connection->insert($sql, array_values($values));
        
        return (int) $this->connection->lastInsertId();
    }

    /**
     * Update records in the database.
     *
     * @param array $values
     * @return int Number of affected rows.
     * @throws DatabaseException
     */
    public function update(array $values): int
    {
        $columns = [];
        $bindings = [];
        
        foreach ($values as $key => $value) {
            $columns[] = "{$key} = ?";
            $bindings[] = $value;
        }
        
        $columnsSql = implode(', ', $columns);
        $sql = "UPDATE {$this->table} SET {$columnsSql}";
        
        if (!empty($this->wheres)) {
            // Re-use logic from toSql for WHERE compilation
            $whereParts = explode('WHERE ', $this->toSql());
            if (count($whereParts) > 1) {
                // Strip ORDER BY/LIMIT if they exist in the extracted WHERE string
                $rawWhere = explode(' ORDER BY', $whereParts[1])[0];
                $rawWhere = explode(' LIMIT', $rawWhere)[0];
                $sql .= " WHERE " . $rawWhere;
            }
            $bindings = array_merge($bindings, $this->bindings['where']);
        }
        
        return $this->connection->update($sql, $bindings);
    }

    /**
     * Delete records from the database.
     *
     * @return int Number of affected rows.
     * @throws DatabaseException
     */
    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $whereParts = explode('WHERE ', $this->toSql());
            if (count($whereParts) > 1) {
                $rawWhere = explode(' ORDER BY', $whereParts[1])[0];
                $rawWhere = explode(' LIMIT', $rawWhere)[0];
                $sql .= " WHERE " . $rawWhere;
            }
        }
        
        return $this->connection->delete($sql, $this->bindings['where']);
    }

    /**
     * Get all flat bindings for execution.
     *
     * @return array
     */
    public function getBindings(): array
    {
        $flatBindings = [];
        foreach ($this->bindings as $type => $bindings) {
            foreach ($bindings as $binding) {
                $flatBindings[] = $binding;
            }
        }
        return $flatBindings;
    }
}