<?php
// Path: app/Modules/Purchasing/Requisitions/Domain/PurchaseRequisition.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Requisitions\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Purchase Requisition (PR)
 * طلب الاحتياج الداخلي المرفوع من أقسام الشركة لطلب شراء بضاعة أو أصول.
 */
class PurchaseRequisition extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'pr_number'        => 'string',
        'requester_id'     => 'integer', // الموظف طالب الاحتياج
        'department_id'    => 'integer', // القسم التابع له
        'request_date'     => 'string',  // YYYY-MM-DD
        'required_date'    => 'string',  // YYYY-MM-DD (متى يحتاجون البضاعة؟)
        'justification'    => 'string',  // مبرر الشراء للمدير المالي
        'total_estimated'  => 'float',   // التكلفة التقديرية
        'status'           => 'string',  // draft, pending_approval, approved, rejected, ordered
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}