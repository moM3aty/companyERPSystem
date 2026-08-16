<?php
// Path: app/Modules/Sales/SalesOrders/Domain/SalesOrder.php

declare(strict_types=1);

namespace App\Modules\Sales\SalesOrders\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Sales Order
 * يمثل أمر البيع المعتمد. يقوم بحجز البضاعة في المخزن حتى يتم شحنها لاحقاً.
 */
class SalesOrder extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'order_no'         => 'string',
        'customer_id'      => 'integer',
        'quotation_id'     => 'integer', // قد يُبنى على عرض سعر سابق
        'order_date'       => 'string',
        'delivery_date'    => 'string',
        'currency_id'      => 'integer',
        'subtotal'         => 'float',
        'discount_total'   => 'float',
        'tax_total'        => 'float',
        'grand_total'      => 'float',
        'status'           => 'string', // 'draft', 'confirmed', 'processing', 'shipped', 'invoiced', 'cancelled'
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}