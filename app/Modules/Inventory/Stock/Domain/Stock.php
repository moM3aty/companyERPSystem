<?php
// Path: app/Modules/Inventory/Stock/Domain/Stock.php

declare(strict_types=1);

namespace App\Modules\Inventory\Stock\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Stock
 * يمثل الرصيد الفعلي لصنف معين داخل مستودع محدد.
 */
class Stock extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'branch_id'         => 'integer',
        'product_id'        => 'integer',
        'warehouse_id'      => 'integer',
        'quantity'          => 'float',
        'reserved_quantity' => 'float',
        'average_cost'      => 'float',
        'last_movement_at'  => 'string',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];

    /**
     * حساب الكمية المتاحة للاستخدام (الكمية الإجمالية - المحجوزة).
     *
     * @return float
     */
    public function getAvailableQuantity(): float
    {
        $qty = (float) $this->getAttribute('quantity');
        $reserved = (float) $this->getAttribute('reserved_quantity');
        return $qty - $reserved;
    }

    /**
     * إضافة كمية للرصيد.
     *
     * @param float $amount
     * @return void
     */
    public function addQuantity(float $amount): void
    {
        $current = (float) $this->getAttribute('quantity');
        $this->setAttribute('quantity', $current + $amount);
    }

    /**
     * خصم كمية من الرصيد.
     *
     * @param float $amount
     * @return void
     */
    public function subtractQuantity(float $amount): void
    {
        $current = (float) $this->getAttribute('quantity');
        $this->setAttribute('quantity', $current - $amount);
    }
}