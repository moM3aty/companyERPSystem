<?php
// Path: app/Compliance/DataRetention/Domain/DataRetentionPolicy.php

declare(strict_types=1);

namespace App\Compliance\DataRetention\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Compliance: Data Retention Policy
 * سياسات الاحتفاظ بالبيانات. تحدد متى يجب أرشفة أو مسح أو إخفاء (Anonymize) البيانات 
 * لتوافق قوانين مثل (GDPR) و (PDPL).
 */
class DataRetentionPolicy extends Entity
{
    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer', // null means system-wide
        'entity_type'       => 'string',  // e.g., 'audit_logs', 'failed_jobs', 'terminated_employees'
        'retention_days'    => 'integer', // مدة الاحتفاظ بالبيانات قبل تطبيق الأكشن
        'action_on_expiry'  => 'string',  // 'delete', 'anonymize', 'archive'
        'is_active'         => 'boolean',
    ];
}