<?php
// Path: app/Core/Organization/CostCenter.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Models\Entity;

/**
 * Enterprise Cost Center Entity
 * مركز التكلفة. أداة محاسبية بحتة لتتبع المصروفات والإيرادات لأقسام أو مشاريع معينة.
 */
class CostCenter extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'code' => 'string',
        'name' => 'string',
        'is_active' => 'boolean',
    ];
}