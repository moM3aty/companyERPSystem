<?php
// Path: app/Modules/Purchasing/PurchaseOrders/Domain/PurchaseOrder.php

declare(strict_types=1);

namespace App\Modules\Purchasing\PurchaseOrders\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Purchase Order
 * ترويسة أمر الشراء (PO Header).
 */
class PurchaseOrder extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                     => 'integer',
        'company_id'             => 'integer',
        'branch_id'              => 'integer',
        'po_number'              => 'string',
        'supplier_id'            => 'integer',
        'order_date'             => 'string', // YYYY-MM-DD
        'expected_delivery_date' => 'string', // YYYY-MM-DD
        'currency_id'            => 'integer',
        'subtotal'               => 'float',
        'discount_total'         => 'float',
        'tax_total'              => 'float',
        'grand_total'            => 'float',
        'status'                 => 'string', // draft, approved, sent, received, cancelled
        'notes'                  => 'string',
        'created_by'             => 'integer',
        'created_at'             => 'string',
        'updated_at'             => 'string',
    ];
}