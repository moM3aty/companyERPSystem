<?php
// Path: app/Modules/Purchasing/Invoices/Domain/PurchaseInvoiceItem.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Invoices\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Purchase Invoice Item
 * تفاصيل أصناف فاتورة المشتريات.
 */
class PurchaseInvoiceItem extends Entity
{
    protected array $casts = [
        'id'                  => 'integer',
        'purchase_invoice_id' => 'integer',
        'product_id'          => 'integer',
        'description'         => 'string',
        'quantity'            => 'float',
        'unit_price'          => 'float',
        'discount_amount'     => 'float',
        'tax_amount'          => 'float',
        'total'               => 'float',
        'warehouse_id'        => 'integer',
    ];
}