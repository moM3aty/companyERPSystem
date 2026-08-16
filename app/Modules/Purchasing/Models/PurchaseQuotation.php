<?php
// Path: app/Modules/Purchasing/Models/PurchaseQuotation.php
declare(strict_types=1);

namespace App\Modules\Purchasing\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Purchase Quotation
 * عرض السعر المستلم من المورد بناءً على طلب تسعير (RFQ).
 */
class PurchaseQuotation extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'rfq_id'           => 'integer',
        'supplier_id'      => 'integer',
        'quotation_number' => 'string',
        'quotation_date'   => 'string',
        'valid_until'      => 'string',
        'total_amount'     => 'float',
        'status'           => 'string', // 'draft', 'submitted', 'awarded', 'rejected'
        'created_by'       => 'integer',
    ];
}