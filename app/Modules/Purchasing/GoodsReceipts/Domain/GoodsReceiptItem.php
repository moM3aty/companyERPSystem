<?php
// Path: app/Modules/Purchasing/GoodsReceipts/Domain/GoodsReceiptItem.php

declare(strict_types=1);

namespace App\Modules\Purchasing\GoodsReceipts\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Goods Receipt Item
 * الأصناف المستلمة فعلياً في المستودع.
 */
class GoodsReceiptItem extends Entity
{
    protected array $casts = [
        'id'                => 'integer',
        'goods_receipt_id'  => 'integer',
        'product_id'        => 'integer',
        'warehouse_id'      => 'integer', // المستودع الذي استلم البضاعة
        'ordered_quantity'  => 'float',   // الكمية المطلوبة (إن وجدت)
        'received_quantity' => 'float',   // الكمية المستلمة فعلياً
        'unit_cost'         => 'float',   // تكلفة الوحدة لإضافتها كقيمة للمخزون
    ];
}