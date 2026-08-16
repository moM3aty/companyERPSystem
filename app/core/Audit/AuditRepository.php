<?php
// Path: app/Core/Audit/AuditRepository.php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Audit Repository
 * يدير عملية إدخال واسترجاع السجلات الخاصة بالتدقيق المالي والإداري بأمان.
 */
class AuditRepository
{
    protected DatabaseManager $db;
    protected string $auditTable = 'audit_logs';
    protected string $activityTable = 'activity_logs';

    /**
     * AuditRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تسجيل حركة تغيير بيانات في قاعدة البيانات (Data Mutation Log).
     *
     * @param array $data
     * @return void
     */
    public function logChange(array $data): void
    {
        $sql = "INSERT INTO {$this->auditTable} 
                (company_id, user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->connection()->insert($sql, [
            $data['company_id'] ?? null,
            $data['user_id'] ?? null,
            $data['action'],
            $data['entity_type'],
            $data['entity_id'],
            isset($data['old_values']) ? json_encode($data['old_values'], JSON_UNESCAPED_UNICODE) : null,
            isset($data['new_values']) ? json_encode($data['new_values'], JSON_UNESCAPED_UNICODE) : null,
            $data['ip_address'] ?? '127.0.0.1',
            $data['user_agent'] ?? 'System',
            date('Y-m-d H:i:s')
        ]);
    }

    /**
     * تسجيل نشاط عام للمستخدم أو السيرفر (Activity Log).
     *
     * @param array $data
     * @return void
     */
    public function logActivity(array $data): void
    {
        $sql = "INSERT INTO {$this->activityTable} 
                (company_id, user_id, activity_type, description, metadata, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->connection()->insert($sql, [
            $data['company_id'] ?? null,
            $data['user_id'] ?? null,
            $data['activity_type'],
            $data['description'],
            isset($data['metadata']) ? json_encode($data['metadata'], JSON_UNESCAPED_UNICODE) : null,
            $data['ip_address'] ?? '127.0.0.1',
            $data['user_agent'] ?? 'System',
            date('Y-m-d H:i:s')
        ]);
    }

    /**
     * استرجاع تاريخ التعديلات لكيان معين (مثال: من قام بتعديل الفاتورة رقم 10؟ ومتى؟).
     *
     * @param string $entityType
     * @param int $entityId
     * @param int $companyId
     * @return array
     */
    public function getEntityHistory(string $entityType, int $entityId, int $companyId): array
    {
        $sql = "SELECT a.*, u.username as user_name 
                FROM {$this->auditTable} a 
                LEFT JOIN users u ON a.user_id = u.id 
                WHERE a.entity_type = ? AND a.entity_id = ? AND a.company_id = ?
                ORDER BY a.created_at DESC";

        return $this->db->connection()->select($sql, [$entityType, $entityId, $companyId]);
    }
}