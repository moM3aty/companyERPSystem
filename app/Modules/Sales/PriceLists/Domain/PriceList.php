<?php
// Path: app/Modules/Sales/PriceLists/Domain/PriceList.php

declare(strict_types=1);

namespace App\Modules\Sales\PriceLists\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Price List
 * قوائم الأسعار المتقدمة. يمكن تعيين قائمة لعملاء الجملة، وأخرى للتجزئة، وأخرى لمواسم التخفيضات.
 */
class PriceList extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'name'           => 'string', // e.g., 'VIP Customers 2026', 'Summer Sale'
        'currency_id'    => 'integer',
        'is_active'      => 'boolean',
        'valid_from'     => 'string', // YYYY-MM-DD HH:MM:SS
        'valid_to'       => 'string', // YYYY-MM-DD HH:MM:SS
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];

    /**
     * هل هذه القائمة سارية المفعول حالياً؟
     */
    public function isValid(): bool
    {
        if (!$this->getAttribute('is_active')) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $from = $this->getAttribute('valid_from');
        $to = $this->getAttribute('valid_to');

        if ($from && $now < $from) return false;
        if ($to && $now > $to) return false;

        return true;
    }
}