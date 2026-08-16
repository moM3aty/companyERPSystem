<?php
// File 1: app/Modules/SupplyChain/Models/LandedCost.php
declare(strict_types=1);

namespace App\Modules\SupplyChain\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Landed Cost (التكلفة الواصلة)
 * يمثل وثيقة توزيع التكاليف الإضافية (شحن، جمارك) على إيصال استلام بضاعة (Goods Receipt).
 */
class LandedCost extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'goods_receipt_id'    => 'integer',
        'allocation_method'   => 'string', // 'value', 'quantity', 'weight', 'volume'
        'total_additional_cost'=> 'float', // إجمالي المصاريف المراد توزيعها
        'status'              => 'string', // 'draft', 'allocated', 'posted'
        'created_by'          => 'integer',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}