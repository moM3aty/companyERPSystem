<?php
// File 10: app/Modules/AdvancedPricing/Models/DiscountRule.php
declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Discount Rule
 * يمثل قاعدة خصم متقدمة (مثال: خصم 10% إذا تجاوزت الكمية 100 حبة، أو تجاوز المبلغ 5000$).
 */
class DiscountRule extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'code'                => 'string',
        'description'         => 'string',
        'discount_percentage' => 'float',
        'min_quantity'        => 'float', // الحد الأدنى للكمية لتطبيق الخصم
        'min_amount'          => 'float', // الحد الأدنى لقيمة الفاتورة لتطبيق الخصم
        'valid_from'          => 'string',
        'valid_to'            => 'string',
        'is_active'           => 'boolean',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}