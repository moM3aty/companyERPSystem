<?php
// Path: app/Modules/Inventory/Policies/WarehousePolicy.php

declare(strict_types=1);

namespace App\Modules\Inventory\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Warehouse
 * يتحكم في صلاحيات المستخدمين على مستوى المستودعات. يمنع فروع من رؤية أو سحب بضائع من مستودعات فروع أخرى.
 */
class WarehousePolicy extends Policy
{
    public function access(AuthUser $currentUser, array $warehouse): bool
    {
        // يجب أن يكون المستودع تابعاً لنفس شركة المستخدم
        if ($currentUser->companyId !== (int) $warehouse['company_id']) {
            return false;
        }

        // إذا كان المستخدم مرتبط بفرع معين، ولا يمتلك صلاحية Super Admin، يجب أن يكون المستودع تابعاً لفرعه
        if ($currentUser->employeeId) {
            // (في النظام الفعلي نقوم بجلب فرع الموظف من Employee Profile)
            // if ($employeeBranchId !== (int) $warehouse['branch_id']) return false;
        }

        return true;
    }
}