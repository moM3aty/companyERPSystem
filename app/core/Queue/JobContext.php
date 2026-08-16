<?php
// Path: app/Core/Queue/JobContext.php

declare(strict_types=1);

namespace App\Core\Queue;

/**
 * Enterprise Job Context DTO
 * يحمل تفاصيل المهمة المسحوبة من قاعدة البيانات أثناء تنفيذها في الـ Worker.
 */
class JobContext
{
    public readonly int $id;
    public readonly string $queue;
    public readonly string $payload;
    public readonly int $attempts;

    /**
     * JobContext constructor.
     *
     * @param int $id
     * @param string $queue
     * @param string $payload
     * @param int $attempts
     */
    public function __construct(int $id, string $queue, string $payload, int $attempts)
    {
        $this->id = $id;
        $this->queue = $queue;
        $this->payload = $payload;
        $this->attempts = $attempts;
    }
}