<?php
// Path: app/Security/Http/Middleware/EnterpriseSecurityMiddleware.php

declare(strict_types=1);

namespace App\Security\Http\Middleware;

use Closure;
use App\Core\Http\Middleware\MiddlewareInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Auth\AuthManager;
use App\Core\Database\DatabaseManager;
use App\Security\SecurityPolicies\Application\SecurityPolicyManager;
use App\Security\DeviceManagement\Application\DeviceTrustService;
use App\Security\ThreatDetection\Application\AnomalyDetectionService;
use App\Core\Exceptions\AuthenticationException;

/**
 * Enterprise Security Shield Middleware
 * يتم وضعه بعد الـ AuthMiddleware. يتأكد من أن المستخدم ليس فقط "مسجل دخول"،
 * بل أن جهازه موثوق، وأن سياسات الشركة (Security Policies) مطبقة عليه بالكامل.
 */
class EnterpriseSecurityMiddleware implements MiddlewareInterface
{
    protected AuthManager $auth;
    protected SecurityPolicyManager $policyManager;
    protected DeviceTrustService $deviceTrust;
    protected AnomalyDetectionService $anomalyDetector;
    protected DatabaseManager $db;

    public function __construct(
        AuthManager $auth,
        SecurityPolicyManager $policyManager,
        DeviceTrustService $deviceTrust,
        AnomalyDetectionService $anomalyDetector,
        DatabaseManager $db
    ) {
        $this->auth = $auth;
        $this->policyManager = $policyManager;
        $this->deviceTrust = $deviceTrust;
        $this->anomalyDetector = $anomalyDetector;
        $this->db = $db;
    }

    public function process(Request $request, Closure $next): Response
    {
        $user = $this->auth->user();

        // إذا لم يكن مسجلاً، نتجاوز الحماية (سيتم إيقافه بواسطة AuthMiddleware أصلاً)
        if (!$user) {
            return $next($request);
        }

        // 1. Device Fingerprinting & Trust Verification
        // يجب أن يرسل הـ Frontend بصمة الجهاز (مثلاً Hash لبيانات المتصفح) في الهيدر
        $deviceId = $request->server('HTTP_X_DEVICE_ID', 'unknown_device');
        $deviceName = $request->server('HTTP_USER_AGENT', 'Unknown Browser');
        $ipAddress = $request->server('REMOTE_ADDR', '127.0.0.1');

        $this->deviceTrust->verifyAndTrackDevice($user->id, $deviceId, $deviceName, $ipAddress);

        // 2. Anomaly Detection (Impossible Travel, New Locations)
        $this->anomalyDetector->detectLoginAnomaly($user->id, $ipAddress);

        // 3. Security Policies Enforcement
        $this->policyManager->enforceConcurrentSessions($user->companyId, $user->id);

        // جلب تاريخ تغيير الباسوورد للمستخدم
        $userData = $this->db->connection()->selectOne("SELECT password_changed_at FROM users WHERE id = ?", [$user->id]);
        $lastChanged = $userData['password_changed_at'] ?? '2000-01-01 00:00:00';
        
        $this->policyManager->checkPasswordExpiry($user->companyId, $lastChanged);

        // 4. المرور بأمان لبقية النظام
        return $next($request);
    }
}