<?php
// Path: app/Core/Queue/DeadLetterQueue.php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Database\DatabaseManager;
use Throwable;

/**
 * Enterprise Dead Letter Queue (DLQ)
 * يتولى مسؤولية استلام المهام التي استنفدت كل محاولات إعادة التشغيل (Retries) 
 * ونقلها إلى جدول `failed_jobs` بدلاً من حذفها لتتم مراجعتها من قبل الإدارة.
 */
class DeadLetterQueue
{
    protected DatabaseManager $db;
    protected string $table = 'failed_jobs';

    /**
     * DeadLetterQueue constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تسجيل المهمة الفاشلة نهائياً.
     *
     * @param string $queue
     * @param string $payload
     * @param Throwable $exception
     * @return void
     */
    public function logFailedJob(string $queue, string $payload, Throwable $exception): void
    {
        $sql = "INSERT INTO {$this->table} (queue, payload, exception, failed_at) VALUES (?, ?, ?, ?)";
        
        $errorDetails = $exception->getMessage() . "\n" . $exception->getTraceAsString();

        $this->db->connection()->insert($sql, [
            $queue,
            $payload,
            $errorDetails,
            date('Y-m-d H:i:s')
        ]);
    }
}