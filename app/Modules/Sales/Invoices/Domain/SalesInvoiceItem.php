<?php
// Path: app/Modules/Sales/Invoices/Domain/SalesInvoiceItem.php

declare(strict_types=1);

namespace App\Modules\Sales\Invoices\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Sales Invoice Item
 * يمثل سطر واحد داخل فاتورة المبيعات.
 */
class SalesInvoiceItem extends Entity
{
    protected array $casts = [
        'id'              => 'integer',
        'invoice_id'      => 'integer',
        'product_id'      => 'integer',
        'description'     => 'string',
        'quantity'        => 'float',
        'unit_price'      => 'float',
        'discount_amount' => 'float',
        'tax_amount'      => 'float',
        'total'           => 'float',
        'warehouse_id'    => 'integer',
    ];
}