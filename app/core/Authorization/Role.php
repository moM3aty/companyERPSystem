<?php
// Path: app/Core/Authorization/Role.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Models\Entity;

/**
 * Enterprise Role Entity
 * يمثل دور وظيفي في النظام (مثال: محاسب، مدير مبيعات، مشرف نظام).
 */
class Role extends Entity
{
    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer', // null means global system role
        'name'           => 'string',
        'description'    => 'string',
        'is_system_role' => 'boolean', // الأدوار النظامية لا يمكن حذفها
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}