<?php
// Path: app/Modules/CRM/Opportunities/Domain/Opportunity.php

declare(strict_types=1);

namespace App\Modules\CRM\Opportunities\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Sales Opportunity
 * يمثل الفرصة البيعية المحتملة للعميل والتي يبنى عليها الـ Sales Forecast.
 */
class Opportunity extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'title'            => 'string',
        'customer_id'      => 'integer', // قد يكون مرتبطاً بـ Customer أو Lead
        'lead_id'          => 'integer', 
        'expected_revenue' => 'float',
        'probability'      => 'integer', // من 0 إلى 100
        'stage'            => 'string',  // 'prospecting', 'qualification', 'proposal', 'negotiation', 'closed_won', 'closed_lost'
        'expected_close_date'=> 'string', // YYYY-MM-DD
        'assigned_to'      => 'integer', // Sales Rep User ID
        'notes'            => 'string',
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];

    /**
     * القيمة المرجحة للفرصة (الإيراد المتوقع × الاحتمالية).
     */
    public function getWeightedRevenue(): float
    {
        $revenue = (float) $this->getAttribute('expected_revenue');
        $probability = (int) $this->getAttribute('probability');

        return $revenue * ($probability / 100);
    }
}