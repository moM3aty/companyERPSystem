<?php
// Path: app/Modules/FixedAssets/Assets/Domain/Asset.php

declare(strict_types=1);

namespace App\Modules\FixedAssets\Assets\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Fixed Asset
 * يمثل الأصل الثابت (سيارة، ماكينة، لابتوب) وقيمته المحاسبية.
 */
class Asset extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                                    => 'integer',
        'company_id'                            => 'integer',
        'branch_id'                             => 'integer',
        'asset_category_id'                     => 'integer',
        'asset_code'                            => 'string',
        'name'                                  => 'string',
        'purchase_date'                         => 'string',
        'purchase_value'                        => 'float',
        'salvage_value'                         => 'float',
        'useful_life_months'                    => 'integer',
        'accumulated_depreciation'              => 'float',
        'net_book_value'                        => 'float',
        'asset_account_id'                      => 'integer', // حساب الأصل في الميزانية
        'accumulated_depreciation_account_id'   => 'integer', // مجمع الإهلاك
        'depreciation_expense_account_id'       => 'integer', // مصروف الإهلاك في الأرباح والخسائر
        'status'                                => 'string', // active, disposed, sold, under_maintenance
        'created_at'                            => 'string',
        'updated_at'                            => 'string',
    ];

    /**
     * التحقق مما إذا كان الأصل قابلاً للإهلاك (لم يهلك بالكامل بعد).
     */
    public function isDepreciable(): bool
    {
        return $this->getAttribute('status') === 'active' && 
               $this->getAttribute('net_book_value') > $this->getAttribute('salvage_value');
    }
}