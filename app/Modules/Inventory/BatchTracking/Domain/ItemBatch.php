<?php
// Path: app/Modules/Inventory/BatchTracking/Domain/ItemBatch.php

declare(strict_types=1);

namespace App\Modules\Inventory\BatchTracking\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Item Batch
 * يمثل تشغيلة إنتاج أو شحنة شراء لها رقم وتاريخ صلاحية محدد (مهم جداً للأدوية والأغذية).
 */
class ItemBatch extends Entity
{
    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'product_id'       => 'integer',
        'batch_number'     => 'string',
        'manufacturing_date'=> 'string', // YYYY-MM-DD
        'expiry_date'      => 'string', // YYYY-MM-DD
        'initial_quantity' => 'float',
        'current_quantity' => 'float',
        'is_active'        => 'boolean',
        'created_at'       => 'string',
    ];

    /**
     * هل البضاعة منتهية الصلاحية؟
     */
    public function isExpired(): bool
    {
        return $this->getAttribute('expiry_date') && $this->getAttribute('expiry_date') < date('Y-m-d');
    }
}