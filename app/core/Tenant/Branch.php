<?php
// Path: app/Core/Tenant/Branch.php

declare(strict_types=1);

namespace App\Core\Tenant;

use App\Core\Models\Entity;

/**
 * Enterprise Branch Entity
 * يمثل فرع تابع لشركة. يسمح بعزل بيانات العمليات (كالمبيعات والمخازن) مع بقاء الهيكل المحاسبي موحد للشركة.
 */
class Branch extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'company_id' => 'integer',
        'code'       => 'string',
        'name'       => 'string',
        'address'    => 'string',
        'is_active'  => 'boolean',
        'created_at' => 'string',
    ];
}