<?php
// Path: app/Core/Reporting/Widget.php

declare(strict_types=1);

namespace App\Core\Reporting;

use App\Core\Models\Entity;

/**
 * Enterprise Widget Entity
 * يمثل عنصر رسومي واحد (مخطط بياني، رقم إجمالي) يوضع داخل الـ Dashboard.
 */
class Widget extends Entity
{
    protected array $casts = [
        'id'            => 'integer',
        'dashboard_id'  => 'integer',
        'name'          => 'string',
        'type'          => 'string', // 'bar_chart', 'pie_chart', 'kpi_card', 'data_table'
        'data_source'   => 'string', // Identifier for the class/query providing data
        'configuration' => 'json',   // Colors, limits, thresholds
        'refresh_rate'  => 'integer', // Seconds to auto-refresh
    ];
}