<?php
// Path: app/Core/Tenant/BranchManager.php

declare(strict_types=1);

namespace App\Core\Tenant;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Branch Manager (Core Logic)
 * يوفر أدوات للتحكم بفروع الشركة من النواة.
 */
class BranchManager
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function isBranchValidForCompany(int $branchId, int $companyId): bool
    {
        $branch = $this->db->connection()->selectOne(
            "SELECT id FROM branches WHERE id = ? AND company_id = ? AND is_active = 1", 
            [$branchId, $companyId]
        );

        return $branch !== null;
    }
}