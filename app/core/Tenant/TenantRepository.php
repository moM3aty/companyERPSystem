<?php
// Path: app/Core/Tenant/TenantRepository.php

declare(strict_types=1);

namespace App\Core\Tenant;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Tenant Repository
 * مستودع قاعدة البيانات الأساسي لجلب تفاصيل الـ Tenant.
 */
class TenantRepository extends BaseRepository
{
    protected string $table = 'companies';
    protected bool $useTenantScope = false; // No scope, this IS the scope origin.

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }
    
    public function findActiveCompany(int $id): ?array
    {
        return $this->newQuery()
            ->where('id', '=', $id)
            ->where('status', '=', 'active')
            ->first();
    }
}