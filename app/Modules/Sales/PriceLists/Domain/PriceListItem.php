<?php
// Path: app/Modules/Sales/PriceLists/Domain/PriceListItem.php

declare(strict_types=1);

namespace App\Modules\Sales\PriceLists\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Price List Item
 * يحدد سعر الصنف داخل القائمة، ويدعم تسعير الكميات (Volume Pricing).
 */
class PriceListItem extends Entity
{
    protected array $casts = [
        'id'               => 'integer',
        'price_list_id'    => 'integer',
        'product_id'       => 'integer',
        'min_quantity'     => 'float', // يبدأ هذا السعر عند شراء هذه الكمية كحد أدنى (Volume Pricing)
        'unit_price'       => 'float', // السعر الثابت
        'discount_percent' => 'float', // أو نسبة خصم من السعر الأساسي للصنف
    ];
}