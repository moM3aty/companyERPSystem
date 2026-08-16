<?php
// Path: app/Core/Reporting/ReportDefinition.php

declare(strict_types=1);

namespace App\Core\Reporting;

use App\Core\Models\Entity;

/**
 * Enterprise Report Definition
 * يمثل هيكل التقرير (Blueprint). يحتوي على استعلام الـ SQL أو الـ Query Builder 
 * والأعمدة المسموح بظهورها.
 */
class ReportDefinition extends Entity
{
    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer', // null means system-wide global report
        'name'             => 'string',
        'module'           => 'string', // 'finance', 'sales', 'inventory'
        'base_query'       => 'string', // Raw SQL or JSON encoded query structure
        'columns'          => 'json',   // Array of columns and their labels
        'allowed_filters'  => 'json',   // Fields that can be used for filtering
        'is_active'        => 'boolean',
    ];
}
