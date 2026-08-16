<?php
// Path: app/Core/Audit/ActivityLog.php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Models\Entity;

/**
 * Enterprise Activity Log Entity
 * يمثل الأنشطة العامة للمستخدمين التي لا ترتبط بجدول محدد (مثل: تسجيل الدخول، تصدير تقرير).
 */
class ActivityLog extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'user_id' => 'integer',
        'activity_type' => 'string', // 'login', 'logout', 'export_report', 'failed_login'
        'description' => 'string',
        'metadata' => 'json', // بيانات إضافية (مثال: اسم التقرير الذي تم تصديره)
        'ip_address' => 'string',
        'user_agent' => 'string',
    ];
}