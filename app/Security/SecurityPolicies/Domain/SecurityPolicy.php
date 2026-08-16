<?php
// Path: app/Security/SecurityPolicies/Domain/SecurityPolicy.php

declare(strict_types=1);

namespace App\Security\SecurityPolicies\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Security Policy Entity
 * القواعد الأمنية المطبقة على مستوى الشركة (Tenant) والتي تلزم المستخدمين بها.
 */
class SecurityPolicy extends Entity
{
    protected array $casts = [
        'id'                             => 'integer',
        'company_id'                     => 'integer',
        'password_expiry_days'           => 'integer', // إجبار تغيير كلمة المرور كل X يوم
        'max_concurrent_sessions'        => 'integer', // أقصى عدد للأجهزة المسجل دخولها في نفس الوقت (0 = غير محدود)
        'session_idle_timeout_minutes'   => 'integer', // إغلاق الجلسة إذا كان المستخدم غير نشط
        'enforce_mfa'                    => 'boolean', // إجبار المصادقة الثنائية لكل مستخدمي الشركة
        'created_at'                     => 'string',
        'updated_at'                     => 'string',
    ];

    public function isMfaEnforced(): bool
    {
        return $this->getAttribute('enforce_mfa') === true;
    }
}