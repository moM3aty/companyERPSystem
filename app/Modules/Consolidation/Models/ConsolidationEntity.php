<?php
// Path: app/Modules/Consolidation/Models/ConsolidationEntity.php

declare(strict_types=1);

namespace App\Modules\Consolidation\Models;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Consolidation Entity
 * يمثل الشركة التابعة (Subsidiary) المربوطة بالمجموعة القابضة ونسبة الملكية.
 */
class ConsolidationEntity extends Entity
{
    protected array $casts = [
        'id'                     => 'integer',
        'consolidation_group_id' => 'integer',
        'company_id'             => 'integer', // الشركة التابعة
        'ownership_percentage'   => 'float',   // نسبة الاستحواذ (مثال: 80% أو 100%)
        'consolidation_method'   => 'string',  // 'full', 'proportional', 'equity'
        'is_active'              => 'boolean',
    ];
}