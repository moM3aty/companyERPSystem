<?php
// Path: app/Modules/Inventory/LandedCost/Domain/LandedCostAllocation.php

declare(strict_types=1);

namespace App\Modules\Inventory\LandedCost\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Landed Cost Allocation
 * نصيب كل صنف داخل إذن الاستلام من التكلفة الإضافية.
 */
class LandedCostAllocation extends Entity
{
    protected array $casts = [
        'id'                     => 'integer',
        'landed_cost_id'         => 'integer',
        'goods_receipt_item_id'  => 'integer',
        'product_id'             => 'integer',
        'allocated_amount'       => 'float', // المبلغ الذي تم تحميله على هذا الصنف
    ];
}