<?php
// Path: app/Modules/Consolidation/Models/ConsolidationGroup.php

declare(strict_types=1);

namespace App\Modules\Consolidation\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Consolidation Group
 * يمثل المجموعة القابضة (Holding Company) التي سيتم تجميع القوائم المالية لشركاتها التابعة.
 */
class ConsolidationGroup extends BaseModel
{
    use HasTimestamps;

    protected array $casts = [
        'id'                => 'integer',
        'name'              => 'string', // اسم المجموعة القابضة (مثال: Global Enterprises)
        'parent_company_id' => 'integer', // الشركة الأم في النظام
        'base_currency_id'  => 'integer', // عملة التجميع الموحدة
        'is_active'         => 'boolean',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];
}