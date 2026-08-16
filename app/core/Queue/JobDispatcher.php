<?php
// Path: app/Core/Queue/JobDispatcher.php

declare(strict_types=1);

namespace App\Core\Queue;

use RuntimeException;

/**
 * Enterprise Job Dispatcher
 * واجهة التفاعل الرئيسية (Facade) التي سيستخدمها المطورون في الـ Controllers لإرسال المهام.
 */
class JobDispatcher
{
    protected QueueManager $queueManager;

    /**
     * JobDispatcher constructor.
     *
     * @param QueueManager $queueManager
     */
    public function __construct(QueueManager $queueManager)
    {
        $this->queueManager = $queueManager;
    }

    /**
     * إرسال كائن المهمة إلى الطابور.
     *
     * @param Job $job كائن المهمة
     * @param string $queue اسم الطابور (الافتراضي 'default')
     * @param int $delay التأخير بالثواني
     * @return int معرف المهمة
     * @throws RuntimeException
     */
    public function dispatch(Job $job, string $queue = 'default', int $delay = 0): int
    {
        // تحويل كائن المهمة إلى نص (Serialization) ليتم حفظه في قاعدة البيانات
        $payload = serialize($job);

        if ($payload === false) {
            throw new RuntimeException("Failed to serialize the job for queueing.");
        }

        return $this->queueManager->push($queue, $payload, $delay);
    }
}