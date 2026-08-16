<?php
// Path: app/Core/Console/Commands/ScheduleRunCommand.php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Scheduler\SchedulerRunner;

/**
 * Enterprise Command: Schedule Run
 * يجب استدعاؤه بواسطة (Linux Cron) مرة واحدة كل دقيقة * * * * * php bin/console schedule:run
 * يقوم بتفقد ما إذا كان هناك مهام مستحقة ويشغلها.
 */
class ScheduleRunCommand
{
    protected SchedulerRunner $runner;

    public function __construct(SchedulerRunner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * تنفيذ الأمر.
     *
     * @param array $args
     * @return int
     */
    public function execute(array $args): int
    {
        echo "\033[36mRunning scheduled tasks...\033[0m\n";

        try {
            $this->runner->runDueTasks();
            echo "\033[32mScheduler executed successfully.\033[0m\n";
            return 0;
        } catch (\Throwable $e) {
            echo "\033[31mScheduler failed:\033[0m " . $e->getMessage() . "\n";
            return 1;
        }
    }
}