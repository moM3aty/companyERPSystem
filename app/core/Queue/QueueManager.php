<?php
// Path: app/Core/Queue/QueueManager.php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Queue Manager (Database Driver)
 * محرك الطوابير المعتمد على قاعدة البيانات. 
 * يتطلب وجود جدول `jobs` يحتوي على (id, queue, payload, attempts, reserved_at, available_at, created_at).
 */
class QueueManager implements QueueInterface
{
    protected DatabaseManager $db;
    protected string $table = 'jobs';

    /**
     * QueueManager constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritDoc
     */
    public function push(string $queue, string $payload, int $delay = 0): int
    {
        $availableAt = time() + max(0, $delay);

        $sql = "INSERT INTO {$this->table} (queue, payload, attempts, reserved_at, available_at, created_at) 
                VALUES (?, ?, 0, NULL, ?, ?)";
        
        $this->db->connection()->insert($sql, [
            $queue,
            $payload,
            $availableAt,
            time()
        ]);

        return (int) $this->db->connection()->lastInsertId();
    }

    /**
     * @inheritDoc
     */
    public function pop(string $queue): ?JobContext
    {
        // 1. العثور على مهمة متاحة وعمل Lock عليها باستخدام reserved_at لتجنب الـ Race Conditions
        $now = time();
        
        $sql = "UPDATE {$this->table} 
                SET reserved_at = ?, attempts = attempts + 1 
                WHERE queue = ? AND reserved_at IS NULL AND available_at <= ? 
                ORDER BY id ASC LIMIT 1";
                
        $affected = $this->db->connection()->update($sql, [$now, $queue, $now]);

        if ($affected === 0) {
            return null; // لا توجد مهام متاحة
        }

        // 2. جلب المهمة التي قمنا بحجزها للتو
        $jobData = $this->db->connection()->selectOne(
            "SELECT * FROM {$this->table} WHERE queue = ? AND reserved_at = ? LIMIT 1",
            [$queue, $now]
        );

        if (!$jobData) {
            return null;
        }

        return new JobContext(
            (int) $jobData['id'],
            $jobData['queue'],
            $jobData['payload'],
            (int) $jobData['attempts']
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): void
    {
        $this->db->connection()->delete("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * @inheritDoc
     */
    public function release(int $id, int $delay = 0): void
    {
        $availableAt = time() + $delay;
        
        // إزالة الحجز وتحديث وقت الإتاحة
        $this->db->connection()->update(
            "UPDATE {$this->table} SET reserved_at = NULL, available_at = ? WHERE id = ?",
            [$availableAt, $id]
        );
    }
}