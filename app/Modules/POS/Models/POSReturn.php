<?php
// Path: app/Modules/POS/Models/POSReturn.php
declare(strict_types=1);

namespace App\Modules\POS\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: POS Return
 * مرتجعات نقاط البيع.
 */
class POSReturn extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'            => 'integer',
        'company_id'    => 'integer',
        'pos_order_id'  => 'integer', // الطلب الأصلي
        'return_amount' => 'float',
        'reason'        => 'string',
        'status'        => 'string', // 'refunded'
        'created_by'    => 'integer',
        'created_at'    => 'string',
        'updated_at'    => 'string',
    ];
}