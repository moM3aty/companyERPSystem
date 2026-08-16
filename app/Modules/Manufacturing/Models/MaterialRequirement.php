<?php
// Path: app/Modules/Manufacturing/Models/MaterialRequirement.php
declare(strict_types=1);

namespace App\Modules\Manufacturing\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;

/**
 * Enterprise Domain Entity: Material Requirement (MRP Result)
 * يمثل الاحتياج الصافي لمادة خام معينة لتنفيذ أمر إنتاج، بعد خصم الرصيد المتاح في المخزن.
 */
class MaterialRequirement extends BaseModel
{
    use HasTenant;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'production_order_id' => 'integer',
        'raw_material_id'     => 'integer',
        'required_quantity'   => 'float',
        'available_stock'     => 'float',
        'net_requirement'     => 'float', // النقص الفعلي الذي يجب شراؤه
        'status'              => 'string', // 'pending', 'ordered', 'fulfilled'
        'created_at'          => 'string',
    ];
}