<?php
// Path: app/Modules/Purchasing/GoodsReceipts/Domain/GoodsReceipt.php

declare(strict_types=1);

namespace App\Modules\Purchasing\GoodsReceipts\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Goods Receipt Note (GRN)
 * يمثل إذن استلام البضاعة الفعلي في المستودع من المورد.
 */
class GoodsReceipt extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'branch_id'         => 'integer',
        'receipt_no'        => 'string',
        'purchase_order_id' => 'integer', // قد يكون مرتبطاً بأمر شراء
        'supplier_id'       => 'integer',
        'receipt_date'      => 'string',
        'reference_doc'     => 'string', // بوليصة الشحن أو رقم إيصال المورد
        'status'            => 'string', // 'draft', 'processed', 'cancelled'
        'received_by'       => 'integer',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];
}