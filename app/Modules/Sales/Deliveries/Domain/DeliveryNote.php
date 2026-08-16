<?php
// Path: app/Modules/Sales/Deliveries/Domain/DeliveryNote.php

declare(strict_types=1);

namespace App\Modules\Sales\Deliveries\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Delivery Note (Dispatch Note)
 * إذن صرف بضاعة للعميل (Delivery).
 */
class DeliveryNote extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'branch_id'      => 'integer',
        'delivery_no'    => 'string',
        'sales_order_id' => 'integer',
        'customer_id'    => 'integer',
        'delivery_date'  => 'string',
        'status'         => 'string', // 'draft', 'shipped', 'delivered', 'cancelled'
        'dispatched_by'  => 'integer',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}