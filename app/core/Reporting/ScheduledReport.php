<?php
// Path: app/Core/Reporting/ScheduledReport.php

declare(strict_types=1);

namespace App\Core\Reporting;

use App\Core\Models\Entity;

/**
 * Enterprise Scheduled Report
 * يمثل إعدادات لتقرير يتم توليده آلياً وإرساله بالإيميل في أوقات محددة (مثال: مبيعات اليوم كل منتصف ليل).
 */
class ScheduledReport extends Entity
{
    protected array $casts = [
        'id'                   => 'integer',
        'company_id'           => 'integer',
        'report_definition_id' => 'integer',
        'cron_expression'      => 'string',
        'email_recipients'     => 'json',
        'format'               => 'string', // 'pdf', 'excel'
        'applied_filters'      => 'json',
        'last_run_at'          => 'string',
        'next_run_at'          => 'string',
        'is_active'            => 'boolean',
    ];
}