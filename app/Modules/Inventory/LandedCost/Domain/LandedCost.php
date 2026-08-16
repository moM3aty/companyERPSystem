<?php
// Path: app/Modules/Inventory/LandedCost/Domain/LandedCost.php

declare(strict_types=1);

namespace App\Modules\Inventory\LandedCost\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Landed Cost
 * التكاليف الإضافية (مثل الجمارك، الشحن، التأمين) التي يتم تحميلها على قيمة بضاعة تم استلامها.
 */
class LandedCost extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'goods_receipt_id'    => 'integer', // إذن الاستلام المرتبط (GRN)
        'purchase_invoice_id' => 'integer', // فاتورة المصاريف (مثلاً فاتورة شركة الشحن)
        'total_cost'          => 'float',   // إجمالي التكلفة الإضافية
        'allocation_method'   => 'string',  // 'by_value', 'by_quantity', 'by_weight'
        'status'              => 'string',  // 'draft', 'posted'
        'journal_entry_id'    => 'integer',
        'created_by'          => 'integer',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}