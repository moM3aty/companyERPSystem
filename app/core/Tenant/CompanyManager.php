<?php
// Path: app/Core/Tenant/CompanyManager.php

declare(strict_types=1);

namespace App\Core\Tenant;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Company Manager (Core Logic)
 * يوفر أدوات للنواة (Core) للتحقق من حالات الشركات وتفعيلها/إيقافها بعيداً عن طبقة الـ Modules.
 */
class CompanyManager
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function isCompanyActive(int $companyId): bool
    {
        $company = $this->db->connection()->selectOne(
            "SELECT status FROM companies WHERE id = ? AND deleted_at IS NULL", 
            [$companyId]
        );

        return $company && $company['status'] === 'active';
    }
}