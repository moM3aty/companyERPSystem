<?php
// Path: app/Core/Console/Commands/QueueWorkCommand.php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Queue\Worker;

/**
 * Enterprise Command: Queue Worker
 * يُبقي السكريبت حياً لسحب ومعالجة المهام الخلفية (إرسال إيميلات، طباعة فواتير، الخ).
 */
class QueueWorkCommand
{
    protected Worker $worker;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }

    /**
     * تنفيذ الأمر.
     *
     * @param array $args
     * @return int
     */
    public function execute(array $args): int
    {
        $queue = $args[0] ?? 'default';

        echo "\n\033[32mQueue Worker started for queue [{$queue}]...\033[0m\n";
        echo "Press Ctrl+C to stop.\n\n";

        try {
            // 0 تعني العمل باستمرار في حلقة مفرغة (Daemon Mode)
            $this->worker->work($queue, 0);
            return 0;
        } catch (\Throwable $e) {
            echo "\n\033[31mWorker crashed:\033[0m " . $e->getMessage() . "\n";
            return 1;
        }
    }
}