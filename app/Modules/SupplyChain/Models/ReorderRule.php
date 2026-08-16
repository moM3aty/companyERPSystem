<?php
// File 2: app/Modules/SupplyChain/Models/ReorderRule.php
declare(strict_types=1);

namespace App\Modules\SupplyChain\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Reorder Rule (قاعدة إعادة الطلب)
 * يحدد سياسة المخزون لكل صنف داخل مستودع معين ليتم طلب الشراء آلياً.
 */
class ReorderRule extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'              => 'integer',
        'company_id'      => 'integer',
        'product_id'      => 'integer',
        'warehouse_id'    => 'integer',
        'min_quantity'    => 'float', // نقطة إعادة الطلب (Reorder Point)
        'max_quantity'    => 'float', // الهدف التخزيني (Target Stock Level)
        'lead_time_days'  => 'integer',// مهلة التوريد المتوقعة
        'is_active'       => 'boolean',
        'created_at'      => 'string',
        'updated_at'      => 'string',
    ];
}