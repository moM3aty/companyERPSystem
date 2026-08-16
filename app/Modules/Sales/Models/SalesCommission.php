<?php
// Path: app/Modules/Sales/Models/SalesCommission.php
declare(strict_types=1);

namespace App\Modules\Sales\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Sales Commission
 * سجل يربط الفاتورة المحصلة بعمولة مندوب المبيعات.
 */
class SalesCommission extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'sales_invoice_id' => 'integer',
        'sales_rep_id'     => 'integer',
        'invoice_amount'   => 'float',
        'commission_rate'  => 'float',
        'commission_amount'=> 'float',
        'status'           => 'string', // 'pending', 'approved', 'paid'
        'created_at'       => 'string',
    ];
}