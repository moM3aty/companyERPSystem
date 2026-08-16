<?php
// Path: app/Core/Validation/Rules/Exists.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Exists Validation Rule
 * تتحقق من أن الـ ID المُرسل (مثلاً customer_id أو category_id) موجود بالفعل وصالح للاستخدام داخل قاعدة البيانات.
 * تدعم الـ Tenant Scoping لضمان عدم إمكانية ربط سجلات بشركة أخرى.
 */
class Exists implements Rule
{
    protected DatabaseManager $db;
    protected string $table;
    protected string $column;
    protected ?int $companyId;

    /**
     * Exists Rule Constructor.
     *
     * @param DatabaseManager $db
     * @param string $table اسم الجدول (مثال: customers)
     * @param string $column اسم العمود (غالباً id)
     * @param int|null $companyId للتحقق داخل نطاق الشركة الحالية
     */
    public function __construct(
        DatabaseManager $db, 
        string $table, 
        string $column = 'id', 
        ?int $companyId = null
    ) {
        $this->db = $db;
        $this->table = $table;
        $this->column = $column;
        $this->companyId = $companyId;
    }

    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true; 
        }

        $sql = "SELECT COUNT(*) as cnt FROM {$this->table} WHERE {$this->column} = ?";
        $bindings = [$value];

        if ($this->companyId !== null) {
            $sql .= " AND company_id = ?";
            $bindings[] = $this->companyId;
        }

        // استثناء السجلات المحذوفة 
        $sql .= " AND deleted_at IS NULL";

        $result = $this->db->connection()->selectOne($sql, $bindings);

        return (int) $result['cnt'] > 0;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The selected {$field} is invalid or does not exist.";
    }
}