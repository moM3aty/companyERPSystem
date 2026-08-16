<?php
// Path: app/Core/Tenant/Company.php

declare(strict_types=1);

namespace App\Core\Tenant;

use App\Core\Models\Entity;

/**
 * Enterprise Company Entity
 * يمثل الشركة المستأجرة (Tenant) للنظام. أعلى مستوى في عزل البيانات.
 */
class Company extends Entity
{
    protected array $casts = [
        'id'                   => 'integer',
        'name'                 => 'string',
        'registration_number'  => 'string',
        'tax_number'           => 'string',
        'base_currency_id'     => 'integer',
        'timezone'             => 'string',
        'status'               => 'string', // 'active', 'suspended'
        'enforce_ip_whitelist' => 'boolean',
        'created_at'           => 'string',
    ];
}