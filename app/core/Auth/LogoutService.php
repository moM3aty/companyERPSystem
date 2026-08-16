<?php
// Path: app/Core/Auth/LogoutService.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Monitoring\Logger;

/**
 * Enterprise Logout Service
 * ينفذ عملية الخروج الآمنة (يغلق الجلسات ويسجل الحدث في الـ Logs).
 */
class LogoutService
{
    protected AuthManager $authManager;
    protected Logger $logger;

    public function __construct(AuthManager $authManager, Logger $logger)
    {
        $this->authManager = $authManager;
        $this->logger = $logger;
    }

    /**
     * تنفيذ تسجيل الخروج.
     *
     * @return void
     */
    public function execute(): void
    {
        $user = $this->authManager->user();

        if ($user) {
            $this->logger->info("User logged out.", ['user_id' => $user->id, 'email' => $user->email]);
        }

        $this->authManager->logout();
    }
}