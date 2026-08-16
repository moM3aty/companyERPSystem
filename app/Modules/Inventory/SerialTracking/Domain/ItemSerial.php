<?php
// Path: app/Modules/Inventory/SerialTracking/Domain/ItemSerial.php

declare(strict_types=1);

namespace App\Modules\Inventory\SerialTracking\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Item Serial
 * يمثل الرقم التسلسلي (Serial Number) لقطعة واحدة مادية من منتج معين (كود فريد للقطعة).
 */
class ItemSerial extends Entity
{
    protected array $casts = [
        'id'            => 'integer',
        'company_id'    => 'integer',
        'product_id'    => 'integer',
        'serial_number' => 'string',
        'warehouse_id'  => 'integer',
        'status'        => 'string', // 'in_stock', 'sold', 'returned', 'defective'
        'received_at'   => 'string', // متى تم استلام هذه القطعة المحددة
        'sold_at'       => 'string', // متى بيعت
        'created_at'    => 'string',
    ];

    public function isInStock(): bool
    {
        return $this->getAttribute('status') === 'in_stock';
    }
}