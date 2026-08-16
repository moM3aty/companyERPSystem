<?php
// Path: app/Core/Outbox/OutboxRepository.php

declare(strict_types=1);

namespace App\Core\Outbox;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Outbox Repository
 * يتعامل مباشرة مع جدول `outbox_messages` لإضافة المهام وجلب غير المعالج منها وتحديث حالتها.
 */
class OutboxRepository
{
    protected DatabaseManager $db;
    protected string $table = 'outbox_messages';

    /**
     * OutboxRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * إضافة حدث جديد للـ Outbox. يجب استدعاء هذه الدالة داخل الـ Transaction الأساسي!
     *
     * @param string $eventName
     * @param array $payload
     * @return int
     */
    public function saveMessage(string $eventName, array $payload): int
    {
        $sql = "INSERT INTO {$this->table} (event_name, payload, is_processed, created_at) VALUES (?, ?, 0, ?)";
        
        $this->db->connection()->insert($sql, [
            $eventName,
            json_encode($payload, JSON_UNESCAPED_UNICODE),
            date('Y-m-d H:i:s')
        ]);

        return (int) $this->db->connection()->lastInsertId();
    }

    /**
     * جلب قائمة بالأحداث التي لم تتم معالجتها (مع عمل Lock عليها لمنع التكرار).
     *
     * @param int $limit
     * @return array
     */
    public function getUnprocessedMessages(int $limit = 50): array
    {
        // استخدام FOR UPDATE يضمن أن معالج (Worker) آخر لن يقوم بسحب نفس الرسائل
        $sql = "SELECT * FROM {$this->table} WHERE is_processed = 0 ORDER BY id ASC LIMIT {$limit} FOR UPDATE";
        
        return $this->db->connection()->select($sql);
    }

    /**
     * تحديث حالة الرسالة لتصبح "تمت المعالجة".
     *
     * @param int $id
     * @return void
     */
    public function markAsProcessed(int $id): void
    {
        $sql = "UPDATE {$this->table} SET is_processed = 1, processed_at = ? WHERE id = ?";
        
        $this->db->connection()->update($sql, [date('Y-m-d H:i:s'), $id]);
    }
}