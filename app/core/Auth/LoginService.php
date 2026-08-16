<?php
// Path: app/Core/Auth/LoginService.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\AuthenticationException;
use App\Core\Monitoring\Logger;

/**
 * Enterprise Login Service
 * ينظم عملية تسجيل الدخول المعقدة (يجمع بين التحقق، الحظر، والمراقبة الأمنية).
 */
class LoginService
{
    protected AuthManager $authManager;
    protected AccountLockService $lockService;
    protected DatabaseManager $db;
    protected Logger $logger;

    public function __construct(
        AuthManager $authManager,
        AccountLockService $lockService,
        DatabaseManager $db,
        Logger $logger
    ) {
        $this->authManager = $authManager;
        $this->lockService = $lockService;
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * تنفيذ محاولة تسجيل الدخول متكاملة أمنياً.
     *
     * @param string $email
     * @param string $password
     * @param string $ipAddress
     * @param bool $issueToken
     * @return AuthUser|string
     * @throws AuthenticationException
     */
    public function execute(string $email, string $password, string $ipAddress, bool $issueToken = false): AuthUser|string
    {
        // 1. جلب بيانات المستخدم المبدئية للتحقق من حالة الحظر
        $userBase = $this->db->connection()->selectOne("SELECT id, email FROM users WHERE email = ? AND deleted_at IS NULL", [$email]);

        if ($userBase) {
            $userId = (int) $userBase['id'];

            if ($this->lockService->isAccountLocked($userId)) {
                $this->logger->warning("Blocked login attempt to a locked account.", ['email' => $email, 'ip' => $ipAddress]);
                throw new AuthenticationException("Your account has been temporarily locked due to multiple failed login attempts. Please try again later.", 403);
            }
        }

        // 2. محاولة تسجيل الدخول
        $result = $this->authManager->authenticate($email, $password, $issueToken);

        // 3. معالجة الفشل
        if ($result === false) {
            $this->logger->warning("Failed login attempt.", ['email' => $email, 'ip' => $ipAddress]);
            
            if ($userBase) {
                $isLockedNow = $this->lockService->recordFailedAttempt((int) $userBase['id']);
                if ($isLockedNow) {
                    $this->logger->alert("Account locked due to brute force.", ['user_id' => $userBase['id'], 'ip' => $ipAddress]);
                    throw new AuthenticationException("Invalid credentials. Your account has now been locked for security.", 403);
                }
            }

            throw new AuthenticationException("Invalid email or password.", 401);
        }

        // 4. معالجة النجاح
        if ($userBase) {
            $this->lockService->clearFailedAttempts((int) $userBase['id']);
            $this->logger->info("Successful login.", ['user_id' => $userBase['id'], 'ip' => $ipAddress]);
        }

        return $result;
    }
}