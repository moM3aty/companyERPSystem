<?php
// Path: app/Security/ThreatDetection/Application/AnomalyDetectionService.php

declare(strict_types=1);

namespace App\Security\ThreatDetection\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Events\EventBus;
use App\Security\ThreatDetection\Domain\Events\SuspiciousActivityEvent;

/**
 * Enterprise Anomaly Detection Service (Login Behavior & Geofencing)
 * يحلل سلوك تسجيل الدخول للمستخدم لاكتشاف ظاهرة "المستحيل الجغرافي" (Impossible Travel).
 */
class AnomalyDetectionService
{
    protected DatabaseManager $db;
    protected EventBus $eventBus;

    public function __construct(DatabaseManager $db, EventBus $eventBus)
    {
        $this->db = $db;
        $this->eventBus = $eventBus;
    }

    public function detectLoginAnomaly(int $userId, string $currentIp): void
    {
        // 1. جلب آخر 5 تسجيلات دخول ناجحة
        $history = $this->db->connection()->select(
            "SELECT ip_address, attempted_at FROM login_history WHERE user_id = ? AND is_success = 1 ORDER BY id DESC LIMIT 5",
            [$userId]
        );

        if (empty($history)) {
            return; // مستخدم جديد، لا يوجد تاريخ لمقارنته
        }

        // 2. تحليل بسيط للـ IP (في النظام الكامل يتم استخدام خدمة GeoIP Service)
        $previousIps = array_column($history, 'ip_address');
        
        if (!in_array($currentIp, $previousIps, true)) {
            
            // في حالة كان الـ IP جديداً كلياً ولم يستخدم من قبل
            // نطلق حدثاً أمنياً (System SOC Alert)
            $this->eventBus->publish(new SuspiciousActivityEvent(
                $userId,
                'new_ip_location',
                "User logged in from an entirely new IP address.",
                ['current_ip' => $currentIp, 'previous_ips' => $previousIps]
            ));

            // يمكن اتخاذ إجراء فوري هنا (مثل طلب MFA إجباري)
        }

        // 3. تحليل السرعة (Brute Force Anomaly)
        // إذا كان هناك أكثر من 10 محاولات دخول فاشلة من IPs مختلفة في آخر ساعة لنفس المستخدم (Password Spraying Attack)
        $failedAttempts = $this->db->connection()->selectOne(
            "SELECT COUNT(DISTINCT ip_address) as cnt FROM login_history 
             WHERE user_id = ? AND is_success = 0 AND attempted_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [$userId]
        );

        if (((int) ($failedAttempts['cnt'] ?? 0)) > 5) {
            $this->eventBus->publish(new SuspiciousActivityEvent(
                $userId,
                'password_spraying',
                "Detected distributed failed logins targeting this user.",
                ['unique_ips_count' => $failedAttempts['cnt']]
            ));
        }
    }
}