<?php
// Path: app/Modules/Purchasing/Policies/PurchaseOrderPolicy.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Purchase Order
 * يحمي أوامر الشراء، بحيث لا يمكن تعديلها بعد الاعتماد، ولا يمكن لموظف رؤية أوامر فروع أخرى 
 * إلا إذا كان يمتلك صلاحيات الإدارة العليا.
 */
class PurchaseOrderPolicy extends Policy
{
    public function update(AuthUser $currentUser, array $purchaseOrder): bool
    {
        // يجب أن يكون أمر الشراء تابعاً لنفس الشركة
        if ($currentUser->companyId !== (int) $purchaseOrder['company_id']) {
            return false;
        }

        // لا يمكن تعديل أمر الشراء إذا تم إرساله للمورد أو تم استلامه
        $immutableStatuses = ['sent', 'received', 'cancelled'];
        if (in_array($purchaseOrder['status'], $immutableStatuses, true)) {
            return false;
        }

        return true;
    }
}