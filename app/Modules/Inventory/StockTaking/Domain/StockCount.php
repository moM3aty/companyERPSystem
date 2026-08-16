<?php
// Path: app/Modules/Inventory/StockTaking/Domain/StockCount.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockTaking\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Stock Count (Physical Inventory)
 * يمثل ترويسة عملية الجرد الفعلي للمستودع في تاريخ محدد.
 */
class StockCount extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'           => 'integer',
        'company_id'   => 'integer',
        'branch_id'    => 'integer',
        'warehouse_id' => 'integer',
        'count_number' => 'string', // e.g., 'STK-2026-08-001'
        'count_date'   => 'string', // YYYY-MM-DD
        'status'       => 'string', // 'draft', 'in_progress', 'completed', 'cancelled'
        'notes'        => 'string',
        'created_by'   => 'integer',
        'approved_by'  => 'integer',
        'created_at'   => 'string',
        'updated_at'   => 'string',
    ];

    public function isCompleted(): bool
    {
        return $this->getAttribute('status') === 'completed';
    }
}