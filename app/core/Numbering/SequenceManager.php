<?php
// Path: app/Core/Numbering/SequenceManager.php

declare(strict_types=1);

namespace App\Core\Numbering;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Sequence Manager
 * يدير إعدادات التسلسلات لكل شركة/فرع لجميع أنواع المستندات (فواتير، قيود، عروض أسعار).
 */
class SequenceManager
{
    protected DatabaseManager $db;
    protected string $table = 'document_sequences';

    /**
     * SequenceManager constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * البحث عن إعدادات تسلسل معين باستخدام كوده.
     *
     * @param string $documentType نوع المستند (مثال: sales_invoice)
     * @param NumberingContext $context
     * @return Sequence|null
     */
    public function findSequence(string $documentType, NumberingContext $context): ?Sequence
    {
        $query = "SELECT * FROM {$this->table} WHERE document_type = ? AND is_active = 1";
        $bindings = [$documentType];

        if ($context->companyId) {
            $query .= " AND (company_id = ? OR company_id IS NULL)";
            $bindings[] = $context->companyId;
        }

        // ترتيب النتائج ليأخذ المخصص للشركة أولاً، ثم العام
        $query .= " ORDER BY company_id DESC LIMIT 1";

        $data = $this->db->connection()->selectOne($query, $bindings);

        return $data ? new Sequence($data) : null;
    }

    /**
     * إنشاء أو تهيئة تسلسل جديد.
     *
     * @param array $data
     * @return int
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $this->db->connection()->insert($sql, array_values($data));
        
        return (int) $this->db->connection()->lastInsertId();
    }
}