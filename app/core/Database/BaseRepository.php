<?php
// Path: app/Core/Database/BaseRepository.php

declare(strict_types=1);

namespace App\Core\Database;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Exceptions\DatabaseException;
use App\Core\Exceptions\NotFoundException;

/**
 * Enterprise Base Repository
 * Provides core CRUD functionality, automatic Multi-Tenant scoping, and Soft Delete handling.
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * The database manager instance.
     *
     * @var DatabaseManager
     */
    protected DatabaseManager $db;

    /**
     * The table associated with the repository.
     * Must be defined in the child class.
     *
     * @var string
     */
    protected string $table;

    /**
     * The primary key for the model/table.
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Does the table use soft deletes (deleted_at)?
     *
     * @var bool
     */
    protected bool $useSoftDeletes = true;

    /**
     * Does the table use multi-tenant architecture (company_id)?
     *
     * @var bool
     */
    protected bool $useTenantScope = true;

    /**
     * The current active tenant ID (Company ID).
     * In a full request lifecycle, this would be set by the TenantMiddleware.
     *
     * @var int|null
     */
    protected ?int $tenantId = null;

    /**
     * BaseRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }


    /**
     * Set the current Tenant ID (Company ID).
     *
     * @param int $tenantId
     * @return self
     */
    public function setTenantId(int $tenantId): self
    {
        $this->tenantId = $tenantId;
        return $this;
    }

    /**
     * Get a fresh QueryBuilder instance pre-configured for the current table.
     * Automatically applies Tenant and Soft Delete scopes.
     *
     * @return QueryBuilder
     */
    protected function newQuery(): QueryBuilder
    {
        $query = new QueryBuilder($this->db->connection());
        $query->table($this->table);

        // Auto-apply Multi-Tenant Scope (Mandatory for ERP security)
        if ($this->useTenantScope && $this->tenantId !== null) {
            $query->where('company_id', '=', $this->tenantId);
        }

        // Auto-apply Soft Deletes Scope
        if ($this->useSoftDeletes) {
            $query->whereNull('deleted_at');
        }

        return $query;
    }


    /**
     * Retrieve all records.
     *
     * @param array $columns
     * @return array
     */
    public function all(array $columns = ['*']): array
    {
        return $this->newQuery()->select($columns)->get();
    }

    /**
     * Find a single record by its primary key.
     *
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function find(int $id, array $columns = ['*']): mixed
    {
        return $this->newQuery()
                    ->select($columns)
                    ->where($this->primaryKey, '=', $id)
                    ->first();
    }

    /**
     * Find a single record or throw a NotFoundException if it doesn't exist.
     *
     * @param int $id
     * @param array $columns
     * @return mixed
     * @throws NotFoundException
     */
    public function findOrFail(int $id, array $columns = ['*']): mixed
    {
        $record = $this->find($id, $columns);

        if (!$record) {
            throw new NotFoundException("Record not found in table [{$this->table}] with ID [{$id}].");
        }

        return $record;
    }

    /**
     * Retrieve a paginated list of records.
     *
     * @param int $perPage
     * @param int $page
     * @param array $columns
     * @return array
     */
    public function paginate(int $perPage = 15, int $page = 1, array $columns = ['*']): array
    {
        $offset = ($page - 1) * $perPage;

        $data = $this->newQuery()
                     ->select($columns)
                     ->limit($perPage)
                     ->offset($offset)
                     ->get();
                     
        // Note: A full pagination implementation would also run a COUNT(*) query 
        // to return total pages, but this keeps it clean for array returns.
        
        return $data;
    }


    /**
     * Create a new record in the database.
     * Automatically injects company_id and created_at if applicable.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        if ($this->useTenantScope && $this->tenantId !== null && !isset($data['company_id'])) {
            $data['company_id'] = $this->tenantId;
        }
        
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->newQuery()->insert($data);
    }

    /**
     * Update an existing record.
     * Automatically injects updated_at if applicable.
     *
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update(int $id, array $data): int
    {
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        return $this->newQuery()
                    ->where($this->primaryKey, '=', $id)
                    ->update($data);
    }

    /**
     * Delete a record.
     * If Soft Deletes are enabled, it performs an update setting 'deleted_at'.
     * Otherwise, it permanently deletes the record.
     *
     * @param int $id
     * @return int
     */
    public function delete(int $id): int
    {
        if ($this->useSoftDeletes) {
            return $this->newQuery()
                        ->where($this->primaryKey, '=', $id)
                        ->update(['deleted_at' => date('Y-m-d H:i:s')]);
        }

        return $this->newQuery()
                    ->where($this->primaryKey, '=', $id)
                    ->delete();
    }
}