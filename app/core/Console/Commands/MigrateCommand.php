<?php
// Path: app/Core/Console/Commands/MigrateCommand.php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Database\Migrator;

/**
 * Enterprise Command: Migrate
 * يقوم بتشغيل محرك الـ Migrations لتطبيق أي تغييرات جديدة على الجداول.
 */
class MigrateCommand
{
    protected Migrator $migrator;

    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    /**
     * تنفيذ الأمر.
     *
     * @param array $args
     * @return int
     */
    public function execute(array $args): int
    {
        echo "\n\033[33mStarting Database Migration...\033[0m\n\n";

        try {
            $ran = $this->migrator->run();

            if (empty($ran)) {
                echo "\033[32mNothing to migrate. Database is up to date.\033[0m\n";
            } else {
                echo "\n\033[32mMigrations completed successfully.\033[0m\n";
            }

            return 0;

        } catch (\Throwable $e) {
            echo "\n\033[31mMigration Failed:\033[0m " . $e->getMessage() . "\n";
            return 1;
        }
    }
}