<?php
// Path: app/Modules/Sales/Quotations/Domain/QuotationItem.php

declare(strict_types=1);

namespace App\Modules\Sales\Quotations\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Quotation Item
 */
class QuotationItem extends Entity
{
    protected array $casts = [
        'id'              => 'integer',
        'quotation_id'    => 'integer',
        'product_id'      => 'integer',
        'description'     => 'string',
        'quantity'        => 'float',
        'unit_price'      => 'float',
        'discount_amount' => 'float',
        'tax_amount'      => 'float',
        'total'           => 'float',
    ];
}