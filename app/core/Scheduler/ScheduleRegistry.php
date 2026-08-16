<?php
// Path: app/Core/Scheduler/ScheduleRegistry.php

declare(strict_types=1);

namespace App\Core\Scheduler;

/**
 * Enterprise Schedule Registry
 * مستودع لتسجيل جميع المهام المجدولة في النظام ليتم استدعاؤها لاحقاً بواسطة الـ Runner.
 */
class ScheduleRegistry
{
    /**
     * @var array<ScheduledTask>
     */
    protected array $tasks = [];

    /**
     * إضافة مهمة جديدة للسجل.
     *
     * @param ScheduledTask $task
     * @return void
     */
    public function add(ScheduledTask $task): void
    {
        $this->tasks[] = $task;
    }

    /**
     * جلب جميع المهام المجدولة.
     *
     * @return array<ScheduledTask>
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}