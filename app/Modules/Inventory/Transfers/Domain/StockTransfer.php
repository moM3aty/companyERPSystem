<?php
// Path: app/Modules/Inventory/Transfers/Domain/StockTransfer.php

declare(strict_types=1);

namespace App\Modules\Inventory\Transfers\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Stock Transfer
 * يمثل ترويسة إذن التحويل بين مستودعين (نقل المخزون).
 */
class StockTransfer extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'branch_id'         => 'integer',
        'transfer_no'       => 'string',
        'from_warehouse_id' => 'integer',
        'to_warehouse_id'   => 'integer',
        'transfer_date'     => 'string',
        'status'            => 'string', // 'draft', 'in_transit', 'completed', 'cancelled'
        'created_by'        => 'integer',
        'received_by'       => 'integer',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];
}