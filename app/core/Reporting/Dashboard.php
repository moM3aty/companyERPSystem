<?php
// Path: app/Core/Reporting/Dashboard.php

declare(strict_types=1);

namespace App\Core\Reporting;

use App\Core\Models\Entity;

/**
 * Enterprise Dashboard Entity
 * يمثل لوحة قيادة مخصصة يمكن للمستخدم أو الشركة إنشاؤها لترتيب الـ Widgets.
 */
class Dashboard extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'company_id' => 'integer',
        'user_id'    => 'integer', // If null, it's a company-wide default dashboard
        'name'       => 'string',
        'layout'     => 'json',    // Grid layout coordinates for widgets
        'is_default' => 'boolean',
    ];
}