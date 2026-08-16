<?php
// Path: app/DisasterRecovery/Backup/Console/Commands/RunBackupCommand.php

declare(strict_types=1);

namespace App\DisasterRecovery\Backup\Console\Commands;

use App\DisasterRecovery\Backup\Application\DatabaseBackupService;

/**
 * Enterprise Command: Run Backup
 * أمر يُشغل من الـ CLI لعمل النسخ الاحتياطي (مثال: php bin/console backup:run)
 */
class RunBackupCommand
{
    protected DatabaseBackupService $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function execute(array $args): int
    {
        echo "\033[36mInitiating Disaster Recovery Backup...\033[0m\n";

        try {
            $path = $this->backupService->runFullBackup();
            echo "\033[32mBackup successfully generated and stored at: {$path}\033[0m\n";
            return 0;
        } catch (\Throwable $e) {
            echo "\033[31mBackup Failed:\033[0m " . $e->getMessage() . "\n";
            return 1;
        }
    }
}