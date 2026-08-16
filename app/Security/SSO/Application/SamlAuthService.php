<?php
// Path: app/Security/SSO/Application/SamlAuthService.php

declare(strict_types=1);

namespace App\Security\SSO\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\AuthenticationException;
use App\Core\Auth\AuthManager;
use App\Core\Auth\AuthUser;
use App\Core\Helpers\Str;

/**
 * Enterprise SAML 2.0 Auth Service
 * يدير معمارية الـ SSO للشركات الكبرى. يستقبل الـ Assertion القادم من الـ Identity Provider (IdP)
 * ويقوم بتسجيل الدخول للموظف بدون باسوورد، مع دعم الـ JIT (Just-In-Time) Provisioning.
 */
class SamlAuthService
{
    protected DatabaseManager $db;
    protected AuthManager $auth;

    public function __construct(DatabaseManager $db, AuthManager $auth)
    {
        $this->db = $db;
        $this->auth = $auth;
    }

    /**
     * معالجة استجابة الـ SAML Assertion.
     * (في النظام الفعلي، تعتمد هذه الدالة على مكتبة فك تشفير وتوثيق الـ XML و الـ X509 Cert).
     *
     * @param string $samlResponse (Base64 Encoded XML)
     * @param int $companyId
     * @return AuthUser
     * @throws AuthenticationException
     */
    public function processSamlResponse(string $samlResponse, int $companyId): AuthUser
    {
        // 1. جلب إعدادات مزود الـ SAML للشركة
        $provider = $this->db->connection()->selectOne(
            "SELECT * FROM sso_providers WHERE company_id = ? AND protocol = 'saml2' AND is_active = 1",
            [$companyId]
        );

        if (!$provider) {
            throw new AuthenticationException("SAML SSO is not configured or disabled for this company.");
        }

        // 2. هنا يتم استدعاء مكتبة الـ SAML (مثل onelogin/php-saml)
        // سنحاكي العملية المعقدة باستخراج الإيميل من الـ Assertion المعتمد.
        // $email = SamlLibrary::validateAndExtractEmail($samlResponse, $provider['x509_certificate']);
        $email = "employee@enterprise.com"; // Simulated extraction
        $firstName = "Enterprise"; // Simulated extraction from SAML attributes
        $lastName = "User";

        if (!$email) {
            throw new AuthenticationException("Invalid SAML Assertion: No email address provided by the Identity Provider.");
        }

        // 3. البحث عن المستخدم في النظام
        $userRecord = $this->db->connection()->selectOne(
            "SELECT * FROM users WHERE email = ? AND company_id = ?",
            [$email, $companyId]
        );

        // 4. Just-In-Time (JIT) Provisioning
        if (!$userRecord) {
            if ((int) $provider['auto_provision_users'] === 1) {
                $userRecord = $this->provisionNewUser($email, $firstName, $lastName, $companyId);
            } else {
                throw new AuthenticationException("SSO Account not found, and auto-provisioning is disabled.");
            }
        }

        if ((int) $userRecord['is_active'] !== 1) {
            throw new AuthenticationException("Your account is disabled.");
        }

        // 5. بناء جلسة الدخول
        $user = new AuthUser(
            (int) $userRecord['id'],
            $companyId,
            $userRecord['username'],
            $userRecord['email'],
            $userRecord['employee_id'] ? (int) $userRecord['employee_id'] : null
        );

        // تسجيل الدخول صراحة في الـ Session
        // $this->auth->login($user); (Assuming method exists to force login without password)

        return $user;
    }

    /**
     * إنشاء حساب مستخدم أوتوماتيكياً (JIT).
     */
    protected function provisionNewUser(string $email, string $firstName, string $lastName, int $companyId): array
    {
        $username = strtolower($firstName . '.' . $lastName);
        $randomPassword = hash('sha256', Str::random(40)); // مستخدم الـ SSO لا يحتاج باسوورد، نضع قيمة مستحيلة الاختراق

        $this->db->connection()->insert(
            "INSERT INTO users (company_id, username, email, password_hash, is_active, created_at) VALUES (?, ?, ?, ?, 1, ?)",
            [$companyId, $username, $email, $randomPassword, date('Y-m-d H:i:s')]
        );

        $id = (int) $this->db->connection()->lastInsertId();

        return $this->db->connection()->selectOne("SELECT * FROM users WHERE id = ?", [$id]);
    }
}