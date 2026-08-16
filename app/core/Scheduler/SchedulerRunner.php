<?php
// Path: app/Core/Scheduler/SchedulerRunner.php

declare(strict_types=1);

namespace App\Core\Scheduler;

use App\Core\Bootstrap\Container;
use App\Core\Contracts\LoggerInterface;
use DateTime;
use Throwable;

/**
 * Enterprise Scheduler Runner
 * المحرك الذي يتم استدعاؤه كل دقيقة عبر الـ Cron Job الفعلي في السيرفر (Linux crontab).
 * يقوم بفحص جميع المهام المجدولة، وتحديد من حان وقت تنفيذها، وتشغيلها بأمان.
 */
class SchedulerRunner
{
    protected ScheduleRegistry $registry;
    protected Container $container;
    protected LoggerInterface $logger;

    /**
     * SchedulerRunner constructor.
     *
     * @param ScheduleRegistry $registry
     * @param Container $container
     * @param LoggerInterface $logger
     */
    public function __construct(ScheduleRegistry $registry, Container $container, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * تشغيل المهام المستحقة.
     *
     * @return void
     */
    public function runDueTasks(): void
    {
        $now = new DateTime('now');
        $tasks = $this->registry->getTasks();
        $tasksRun = 0;

        $this->logger->info("Scheduler started checking for due tasks at {$now->format('Y-m-d H:i:s')}.");

        foreach ($tasks as $task) {
            $cron = new CronExpression($task->getExpression());

            if ($cron->isDue($now)) {
                $this->logger->info("Running scheduled task: " . $task->getDescription());
                
                try {
                    $task->run($this->container);
                    $tasksRun++;
                } catch (Throwable $e) {
                    // التقاط الأخطاء لضمان عدم توقف باقي المهام المجدولة
                    $this->logger->error("Scheduled task failed: " . $task->getDescription(), [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        $this->logger->info("Scheduler finished. Total tasks executed: {$tasksRun}.");
    }
}