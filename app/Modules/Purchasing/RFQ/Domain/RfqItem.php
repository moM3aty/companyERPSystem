<?php
// Path: app/Modules/Purchasing/RFQ/Domain/RfqItem.php

declare(strict_types=1);

namespace App\Modules\Purchasing\RFQ\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: RFQ Item
 * الأصناف المطلوبة في طلب التسعير.
 */
class RfqItem extends Entity
{
    protected array $casts = [
        'id'          => 'integer',
        'rfq_id'      => 'integer',
        'product_id'  => 'integer',
        'description' => 'string',
        'quantity'    => 'float',
    ];
}