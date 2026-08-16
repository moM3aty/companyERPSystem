<?php
// Path: app/Modules/Sales/Models/SalesReturn.php
declare(strict_types=1);

namespace App\Modules\Sales\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Sales Return (RMA)
 * يمثل الإذن اللوجستي لاستلام بضاعة مرتجعة من العميل (إعادتها للمخازن).
 */
class SalesReturn extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'return_no'   => 'string',
        'customer_id' => 'integer',
        'invoice_id'  => 'integer', // الفاتورة الأصلية (اختياري)
        'return_date' => 'string',
        'status'      => 'string', // 'pending', 'received', 'rejected'
        'received_by' => 'integer',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}