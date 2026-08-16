<?php
// Path: app/Modules/Purchasing/Policies/SupplierPolicy.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Policies;

use App\Core\Authorization\Policy;
use App\Core\Auth\AuthUser;

/**
 * Enterprise Policy: Supplier
 * يحدد صلاحيات عرض أو تعديل بيانات الموردين لضمان بقائها داخل نطاق الشركة الحالية.
 */
class SupplierPolicy extends Policy
{
    public function view(AuthUser $currentUser, array $supplier): bool
    {
        return $currentUser->companyId === (int) $supplier['company_id'];
    }

    public function update(AuthUser $currentUser, array $supplier): bool
    {
        // المدراء وموظفو المشتريات يحق لهم التعديل بشرط تطابق الـ Tenant
        return $currentUser->companyId === (int) $supplier['company_id'];
    }
}