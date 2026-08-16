<?php
// Path: app/Security/SecurityPolicies/Application/SecurityPolicyManager.php

declare(strict_types=1);

namespace App\Security\SecurityPolicies\Application;

use App\Security\SecurityPolicies\Domain\SecurityPolicyRepositoryInterface;
use App\Security\SessionManagement\Application\SessionRevocationService;
use App\Core\Exceptions\AuthorizationException;

/**
 * Enterprise Security Policy Manager
 * ينفذ قرارات صارمة (مثل قتل الجلسات الزائدة) بناءً على إعدادات السياسة الأمنية.
 */
class SecurityPolicyManager
{
    protected SecurityPolicyRepositoryInterface $policyRepo;
    protected SessionRevocationService $revocationService;

    public function __construct(
        SecurityPolicyRepositoryInterface $policyRepo,
        SessionRevocationService $revocationService
    ) {
        $this->policyRepo = $policyRepo;
        $this->revocationService = $revocationService;
    }

    public function enforceConcurrentSessions(int $companyId, int $userId): void
    {
        $policy = $this->policyRepo->getPolicyForCompany($companyId);
        $maxSessions = (int) $policy->getAttribute('max_concurrent_sessions');

        if ($maxSessions <= 0) {
            return; // غير محدود
        }

        // إذا تجاوز العدد المسموح، نقوم بطرد (قتل) أقدم الجلسات أوتوماتيكياً
        $this->revocationService->revokeOldestSessions($userId, $maxSessions);
    }

    public function checkPasswordExpiry(int $companyId, string $passwordLastChangedAt): void
    {
        $policy = $this->policyRepo->getPolicyForCompany($companyId);
        $expiryDays = (int) $policy->getAttribute('password_expiry_days');

        if ($expiryDays <= 0) {
            return;
        }

        $lastChanged = strtotime($passwordLastChangedAt);
        $expiryDate = $lastChanged + ($expiryDays * 86400);

        if (time() > $expiryDate) {
            throw new AuthorizationException(
                "Security Policy Violation: Your password has expired. You must change it before continuing.",
                403 // 403 Forbidden with specific message to trigger frontend redirect
            );
        }
    }
}