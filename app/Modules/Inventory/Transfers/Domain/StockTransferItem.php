<?php
// Path: app/Modules/Inventory/Transfers/Domain/StockTransferItem.php

declare(strict_types=1);

namespace App\Modules\Inventory\Transfers\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Stock Transfer Item
 * يمثل السطور (الأصناف) المنقولة في إذن التحويل المخزني.
 */
class StockTransferItem extends Entity
{
    protected array $casts = [
        'id'                => 'integer',
        'stock_transfer_id' => 'integer',
        'product_id'        => 'integer',
        'quantity'          => 'float',
        'unit_cost'         => 'float', // يتم نقل التكلفة المتوسطة الحالية من المستودع المصدر
    ];
}