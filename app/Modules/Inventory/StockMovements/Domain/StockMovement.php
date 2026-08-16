<?php
// Path: app/Modules/Inventory/StockMovements/Domain/StockMovement.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockMovements\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Stock Movement
 * يمثل حركة (وارد/منصرف) تسجل في دفتر الصنف (Item Ledger) لغرض التدقيق.
 * هذه البيانات لا تُعدل ولا تُحذف (Immutable).
 */
class StockMovement extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'branch_id'      => 'integer',
        'product_id'     => 'integer',
        'warehouse_id'   => 'integer',
        'movement_type'  => 'string', // IN, OUT, TRANSFER, ADJUSTMENT
        'quantity'       => 'float',
        'balance_after'  => 'float',  // الرصيد بعد هذه الحركة
        'unit_cost'      => 'float',
        'reference_type' => 'string', // e.g., 'purchase_receipt', 'sales_delivery'
        'reference_id'   => 'integer',
        'notes'          => 'string',
        'created_by'     => 'integer',
        'created_at'     => 'string',
        'updated_at'     => 'string', // عادة تكون مطابقة لـ created_at لأنها لا تعدل
    ];
}