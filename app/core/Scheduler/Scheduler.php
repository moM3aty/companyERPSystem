<?php
// Path: app/Core/Scheduler/Scheduler.php

declare(strict_types=1);

namespace App\Core\Scheduler;

use Closure;

/**
 * Enterprise Scheduler Facade
 * الواجهة البرمجية (API) التي يستخدمها المطور لتعريف المهام وجدولتها بسهولة.
 */
class Scheduler
{
    protected ScheduleRegistry $registry;

    /**
     * Scheduler constructor.
     *
     * @param ScheduleRegistry $registry
     */
    public function __construct(ScheduleRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * جدولة دالة مجهولة (Closure) للتنفيذ.
     *
     * @param Closure $callback
     * @return ScheduledTask
     */
    public function call(Closure $callback): ScheduledTask
    {
        $task = new ScheduledTask($callback);
        $this->registry->add($task);
        
        return $task;
    }

    /**
     * جدولة كلاس معين (عادة كلاس Job) للتنفيذ.
     *
     * @param string $jobClass
     * @return ScheduledTask
     */
    public function job(string $jobClass): ScheduledTask
    {
        $task = new ScheduledTask($jobClass);
        $this->registry->add($task);
        
        return $task;
    }
}