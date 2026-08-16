<?php
// Path: app/Core/Documents/DocumentManager.php

declare(strict_types=1);

namespace App\Core\Documents;

use App\Core\Database\DatabaseManager;
use App\Core\Files\FileManager;
use App\Core\Auth\AuthManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Document Manager (Facade)
 * المايسترو الذي يدير كل العمليات المعقدة للمستندات (الإنشاء، القفل، أخذ الإصدارات، والمرفقات).
 */
class DocumentManager
{
    protected DatabaseManager $db;
    protected FileManager $fileManager;
    protected AuthManager $auth;

    public function __construct(DatabaseManager $db, FileManager $fileManager, AuthManager $auth)
    {
        $this->db = $db;
        $this->fileManager = $fileManager;
        $this->auth = $auth;
    }

    /**
     * محاولة قفل المستند لمنع الآخرين من تعديله.
     *
     * @param int $documentId
     * @param int $lockDurationMinutes
     * @return bool
     * @throws BusinessException
     */
    public function acquireLock(int $documentId, int $lockDurationMinutes = 15): bool
    {
        $userId = $this->auth->user()?->id;
        if (!$userId) {
            throw new BusinessException("User must be authenticated to lock a document.");
        }

        // تنظيف الأقفال المنتهية أولاً
        $this->db->connection()->delete("DELETE FROM document_locks WHERE expires_at <= ?", [date('Y-m-d H:i:s')]);

        // التحقق من وجود قفل نشط لشخص آخر
        $existingLock = $this->db->connection()->selectOne(
            "SELECT locked_by FROM document_locks WHERE document_id = ?", 
            [$documentId]
        );

        if ($existingLock && (int) $existingLock['locked_by'] !== $userId) {
            throw new BusinessException("This document is currently being edited by another user.", 409);
        }

        // إذا كان القفل لنفس المستخدم، نقوم بتحديث وقت الانتهاء، وإلا ننشئ قفلاً جديداً
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$lockDurationMinutes} minutes"));

        if ($existingLock) {
            $this->db->connection()->update(
                "UPDATE document_locks SET expires_at = ? WHERE document_id = ?",
                [$expiresAt, $documentId]
            );
        } else {
            $this->db->connection()->insert(
                "INSERT INTO document_locks (document_id, locked_by, locked_at, expires_at) VALUES (?, ?, ?, ?)",
                [$documentId, $userId, date('Y-m-d H:i:s'), $expiresAt]
            );
        }

        return true;
    }

    /**
     * فك قفل المستند.
     *
     * @param int $documentId
     * @return void
     */
    public function releaseLock(int $documentId): void
    {
        $userId = $this->auth->user()?->id;
        if ($userId) {
            $this->db->connection()->delete(
                "DELETE FROM document_locks WHERE document_id = ? AND locked_by = ?", 
                [$documentId, $userId]
            );
        }
    }

    /**
     * إنشاء إصدار جديد (Snapshot) من المستند للاحتفاظ بالتاريخ.
     *
     * @param int $documentId
     * @param array $payload البيانات الكاملة للمستند (رأس + تفاصيل)
     * @param string $reason سبب التعديل
     * @return void
     */
    public function createVersion(int $documentId, array $payload, string $reason = 'Manual Update'): void
    {
        $userId = $this->auth->user()?->id ?? 0;

        // جلب آخر رقم إصدار
        $lastVersion = $this->db->connection()->selectOne(
            "SELECT MAX(version_number) as v_num FROM document_versions WHERE document_id = ?",
            [$documentId]
        );
        $newVersionNumber = ((int) ($lastVersion['v_num'] ?? 0)) + 1;

        $this->db->connection()->insert(
            "INSERT INTO document_versions (document_id, version_number, payload, changed_by, change_reason, created_at) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $documentId, 
                $newVersionNumber, 
                json_encode($payload, JSON_UNESCAPED_UNICODE), 
                $userId, 
                $reason, 
                date('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * ربط مستند بمستند آخر (مثال: فاتورة مرتبطة بأمر شراء).
     *
     * @param int $sourceId
     * @param int $targetId
     * @param string $relationType
     * @return void
     */
    public function linkDocuments(int $sourceId, int $targetId, string $relationType = 'generated_from'): void
    {
        $this->db->connection()->insert(
            "INSERT INTO document_references (source_document_id, target_document_id, reference_type, created_at) 
             VALUES (?, ?, ?, ?)",
            [$sourceId, $targetId, $relationType, date('Y-m-d H:i:s')]
        );
    }
}