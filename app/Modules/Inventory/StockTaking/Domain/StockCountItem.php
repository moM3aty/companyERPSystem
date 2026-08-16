<?php
// Path: app/Modules/Inventory/StockTaking/Domain/StockCountItem.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockTaking\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Stock Count Item
 * يمثل الأصناف المجرودة والفرق بين رصيد النظام والرصيد الفعلي.
 */
class StockCountItem extends Entity
{
    protected array $casts = [
        'id'               => 'integer',
        'stock_count_id'   => 'integer',
        'product_id'       => 'integer',
        'system_quantity'  => 'float', // الكمية كما هي مسجلة في السيرفر وقت الجرد
        'counted_quantity' => 'float', // الكمية الفعلية التي عدها أمين المستودع
        'difference'       => 'float', // (Counted - System)
        'unit_cost'        => 'float', // تكلفة الوحدة وقت الجرد لتقييم العجز/الزيادة
    ];
}