<?php
// Path: app/Core/Organization/DepartmentManager.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Database\DatabaseManager;
use App\Core\Tenant\TenantContext;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Department Manager
 * مدير عمليات الأقسام بالتحديد. ينفذ مهام مثل (تغيير مدير القسم) و تحديث الشجرة التنظيمية.
 */
class DepartmentManager
{
    protected DatabaseManager $db;
    protected TenantContext $tenant;
    protected OrganizationTree $treeBuilder;

    public function __construct(DatabaseManager $db, TenantContext $tenant, OrganizationTree $treeBuilder)
    {
        $this->db = $db;
        $this->tenant = $tenant;
        $this->treeBuilder = $treeBuilder;
    }

    /**
     * تعيين مدير جديد لقسم أو قطاع.
     *
     * @param int $nodeId معرف القسم/القطاع
     * @param int $managerId معرف الموظف/المدير
     * @return void
     * @throws BusinessException
     */
    public function assignManager(int $nodeId, int $managerId): void
    {
        $companyId = $this->tenant->requireTenant()->companyId;

        // التحقق من أن الموظف يتبع للشركة
        $user = $this->db->connection()->selectOne("SELECT id FROM users WHERE id = ? AND company_id = ?", [$managerId, $companyId]);
        
        if (!$user) {
            throw new BusinessException("The selected manager does not belong to the current company.", 422);
        }

        $affected = $this->db->connection()->update(
            "UPDATE organization_nodes SET manager_id = ?, updated_at = ? WHERE id = ? AND company_id = ?",
            [$managerId, date('Y-m-d H:i:s'), $nodeId, $companyId]
        );

        if ($affected === 0) {
            throw new BusinessException("Department not found.", 404);
        }

        // مسح الكاش لإعادة بناء الشجرة الإدارية بالمدير الجديد
        $this->treeBuilder->clearTreeCache($companyId);
    }
}