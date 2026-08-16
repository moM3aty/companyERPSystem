<?php
// Path: app/Core/Organization/CostCenterManager.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Cost Center Manager
 * يدير إنشاء والتحقق من مراكز التكلفة المحاسبية.
 */
class CostCenterManager
{
    protected DatabaseManager $db;
    protected TenantContext $tenant;

    public function __construct(DatabaseManager $db, TenantContext $tenant)
    {
        $this->db = $db;
        $this->tenant = $tenant;
    }

    /**
     * جلب جميع مراكز التكلفة المفعلة للشركة.
     *
     * @return array
     */
    public function getActiveCostCenters(): array
    {
        $companyId = $this->tenant->requireTenant()->companyId;

        $rows = $this->db->connection()->select(
            "SELECT * FROM cost_centers WHERE company_id = ? AND is_active = 1",
            [$companyId]
        );

        return array_map(fn($row) => new CostCenter($row), $rows);
    }

    /**
     * التحقق من صحة مركز التكلفة قبل استخدامه في قيد محاسبي.
     *
     * @param int $costCenterId
     * @return void
     * @throws BusinessException
     */
    public function validateForPosting(int $costCenterId): void
    {
        $companyId = $this->tenant->requireTenant()->companyId;

        $cc = $this->db->connection()->selectOne(
            "SELECT is_active FROM cost_centers WHERE id = ? AND company_id = ?",
            [$costCenterId, $companyId]
        );

        if (!$cc || (int) $cc['is_active'] !== 1) {
            throw new BusinessException("Invalid or inactive Cost Center [ID: {$costCenterId}].", 422);
        }
    }
}