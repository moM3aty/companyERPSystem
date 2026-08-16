<?php
// Path: app/Modules/Manufacturing/Routings/Domain/Routing.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Routings\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Routing (مسار التصنيع)
 * يمثل خطة سير المنتج على مراكز العمل المختلفة حتى يكتمل تصنيعه.
 */
class Routing extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'product_id'  => 'integer', // المنتج النهائي المستهدف
        'code'        => 'string',  // e.g., 'ROUT-DESK-01'
        'name'        => 'string',
        'is_active'   => 'boolean',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}