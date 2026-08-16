<?php
// Path: app/Core/Numbering/SequenceGenerator.php

declare(strict_types=1);

namespace App\Core\Numbering;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\DatabaseException;

/**
 * Enterprise Sequence Generator
 * القلب النابض الذي يضمن الترقيم الدقيق والآمن.
 * يستخدم (Pessimistic Locking / FOR UPDATE) لمنع تكرار الأرقام تحت أي ضغط أو تزامن.
 */
class SequenceGenerator
{
    protected DatabaseManager $db;
    protected string $table = 'document_sequences';

    /**
     * SequenceGenerator constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * توليد الرقم التسلسلي التالي بضمان أمني بنسبة 100% (Row Locking).
     *
     * @param int $sequenceId معرف التسلسل في قاعدة البيانات
     * @param int $step مقدار الزيادة (غالباً 1)
     * @return int الرقم الجديد
     * @throws DatabaseException|BusinessException
     */
    public function getNextSequenceNumber(int $sequenceId, int $step = 1): int
    {
        $connection = $this->db->connection();
        
        // التحقق من أننا داخل Transaction، وإلا نفتح واحد جديد لحماية القفل
        $inTransaction = $connection->getTransactionLevel() > 0;
        
        if (!$inTransaction) {
            $connection->beginTransaction();
        }

        try {
            // 1. القفل المتشائم (Pessimistic Lock) لمنع أي عملية أخرى من قراءة أو تعديل هذا السجل
            $sql = "SELECT current_value FROM {$this->table} WHERE id = ? FOR UPDATE";
            $row = $connection->selectOne($sql, [$sequenceId]);

            if (!$row) {
                throw new BusinessException("Sequence with ID {$sequenceId} not found.", 500);
            }

            // 2. الحساب
            $nextValue = (int) $row['current_value'] + $step;

            // 3. التحديث
            $updateSql = "UPDATE {$this->table} SET current_value = ?, updated_at = ? WHERE id = ?";
            $connection->update($updateSql, [$nextValue, date('Y-m-d H:i:s'), $sequenceId]);

            if (!$inTransaction) {
                $connection->commit();
            }

            return $nextValue;

        } catch (\Throwable $e) {
            if (!$inTransaction) {
                $connection->rollBack();
            }
            throw new DatabaseException("Failed to generate sequence number: " . $e->getMessage(), [], $e);
        }
    }
}