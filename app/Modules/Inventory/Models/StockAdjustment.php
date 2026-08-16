<?php
// Path: app/Modules/Inventory/Models/StockAdjustment.php

declare(strict_types=1);

namespace App\Modules\Inventory\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Stock Adjustment
 * يمثل مستند تسوية جردية فردية (تالف، هالك، تسوية عجز) يختلف عن الجرد الشامل.
 */
class StockAdjustment extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'warehouse_id'     => 'integer',
        'adjustment_no'    => 'string', // e.g., 'ADJ-2026-001'
        'adjustment_date'  => 'string', // YYYY-MM-DD
        'reason'           => 'string', // 'shrinkage', 'damage', 'found'
        'status'           => 'string', // 'draft', 'posted', 'cancelled'
        'journal_entry_id' => 'integer', // القيد المحاسبي لفرق التسوية
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}