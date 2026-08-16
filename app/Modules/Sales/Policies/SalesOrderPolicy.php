<?php
// Path: app/Modules/Sales/Policies/SalesOrderPolicy.php

declare(strict_types=1);

namespace App\Modules\Sales\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Sales Order
 * يحدد من يحق له اعتماد أو تعديل أوامر البيع.
 */
class SalesOrderPolicy extends Policy
{
    public function view(AuthUser $currentUser, array $order): bool
    {
        // يجب أن يكون أمر البيع تابعاً لنفس الشركة
        if ($currentUser->companyId !== (int) $order['company_id']) {
            return false;
        }

        // إذا كانت هناك سياسة فروع (Branch Level Security) يتم تطبيقها هنا
        return true;
    }

    public function update(AuthUser $currentUser, array $order): bool
    {
        if (!$this->view($currentUser, $order)) {
            return false;
        }

        // لا يمكن تعديل أوامر البيع التي تم شحنها أو فوترتها
        $immutableStatuses = ['shipped', 'invoiced', 'cancelled'];
        if (in_array($order['status'], $immutableStatuses, true)) {
            return false;
        }

        return true;
    }
}