<?php
// Path: app/Core/Reporting/Report.php

declare(strict_types=1);

namespace App\Core\Reporting;

use App\Core\Models\Entity;

/**
 * Enterprise Report Entity
 * يمثل نسخة (Instance) من تقرير تم إنشاؤه مسبقاً وحفظه (مثلاً تقرير أرباح شهر أغسطس الذي تم تصديره).
 */
class Report extends Entity
{
    protected array $casts = [
        'id'                   => 'integer',
        'company_id'           => 'integer',
        'report_definition_id' => 'integer',
        'generated_by'         => 'integer',
        'file_path'            => 'string',
        'format'               => 'string', // 'pdf', 'csv', 'excel'
        'filters_applied'      => 'json',
        'status'               => 'string', // 'pending', 'processing', 'completed', 'failed'
        'created_at'           => 'string',
    ];
}