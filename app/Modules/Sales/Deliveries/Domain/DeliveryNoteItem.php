<?php
// Path: app/Modules/Sales/Deliveries/Domain/DeliveryNoteItem.php

declare(strict_types=1);

namespace App\Modules\Sales\Deliveries\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Delivery Note Item
 * الأصناف المنصرفة من المستودع.
 */
class DeliveryNoteItem extends Entity
{
    protected array $casts = [
        'id'                 => 'integer',
        'delivery_note_id'   => 'integer',
        'product_id'         => 'integer',
        'warehouse_id'       => 'integer', // المستودع المنصرف منه
        'ordered_quantity'   => 'float',
        'delivered_quantity' => 'float',
    ];
}