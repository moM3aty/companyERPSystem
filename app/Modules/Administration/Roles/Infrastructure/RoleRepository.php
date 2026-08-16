<?php
// Path: app/Modules/Administration/Roles/Infrastructure/RoleRepository.php

declare(strict_types=1);

namespace App\Modules\Administration\Roles\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Administration\Roles\Domain\RoleRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Role
 */
class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    protected string $table = 'roles';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Roles are usually hard-deleted if not linked

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $name, int $companyId): ?array
    {
        $result = $this->newQuery()
                       ->where('name', '=', $name)
                       ->where('company_id', '=', $companyId)
                       ->first();

        return $result ?: null;
    }
}