<?php
// Path: app/Core/Workflow/Approval/SlaManager.php

declare(strict_types=1);

namespace App\Core\Workflow\Approval;

use DateTime;

/**
 * Enterprise SLA Manager
 * يقوم باحتساب وقت انتهاء المهلة (Deadline) لخطوات الموافقة بدقة.
 * في الأنظمة الأكثر تعقيداً، هذا الكلاس يتجاهل عطلات نهاية الأسبوع والعطلات الرسمية.
 */
class SlaManager
{
    /**
     * حساب الموعد النهائي (Deadline) بناءً على عدد الساعات المسموح بها.
     *
     * @param int $slaHours
     * @param string $startDateTime (Y-m-d H:i:s)
     * @return string|null
     */
    public function calculateDeadline(int $slaHours, string $startDateTime): ?string
    {
        if ($slaHours <= 0) {
            return null; // لا يوجد مهلة محددة
        }

        try {
            $date = new DateTime($startDateTime);
            $date->modify("+{$slaHours} hours");
            
            // يمكن هنا إضافة كود يتخطى أيام الإجازة (الجمعة/السبت) 
            // عن طريق عمل حلقة دوران تضيف يوماً إذا صادف الـ Deadline إجازة.

            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}