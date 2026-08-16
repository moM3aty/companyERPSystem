<?php
// Path: app/Modules/Sales/Promotions/Domain/Promotion.php

declare(strict_types=1);

namespace App\Modules\Sales\Promotions\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;

/**
 * Enterprise Domain Entity: Promotion (Sales Discount Rules)
 * يمثل العروض الترويجية الموقوتة (مثل تخفيض 10% لفترة محددة).
 */
class Promotion extends BaseModel
{
    use HasTenant;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'product_id'       => 'integer', // قد يكون null إذا كان العرض على كل المنتجات
        'name'             => 'string',
        'discount_percent' => 'float',
        'fixed_price'      => 'float',
        'start_date'       => 'string', // YYYY-MM-DD HH:MM:SS
        'end_date'         => 'string', // YYYY-MM-DD HH:MM:SS
        'is_active'        => 'boolean',
    ];

    /**
     * التحقق مما إذا كان العرض الترويجي نشطاً وسارياً الآن.
     */
    public function isValidNow(): bool
    {
        if (!$this->getAttribute('is_active')) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $start = $this->getAttribute('start_date');
        $end = $this->getAttribute('end_date');

        if ($start && $now < $start) return false;
        if ($end && $now > $end) return false;

        return true;
    }
}