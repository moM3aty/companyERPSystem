<?php
// Path: app/Modules/FixedAssets/Depreciation/Domain/DepreciationRecord.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Depreciation\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Depreciation Record
 * يمثل سجل إهلاك دوري (غالباً شهري) لأصل ثابت.
 */
class DepreciationRecord extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'asset_id'            => 'integer',
        'period_year'         => 'integer',
        'period_month'        => 'integer',
        'depreciation_amount' => 'float',
        'journal_entry_id'    => 'integer', // يتم ربطه بالقيد المحاسبي المولد آلياً
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}