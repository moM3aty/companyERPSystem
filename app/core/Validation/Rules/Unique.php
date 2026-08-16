<?php
// Path: app/Core/Validation/Rules/Unique.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Unique Validation Rule
 * تتحقق من أن القيمة المدخلة غير موجودة مسبقاً في جدول معين (مثال: بريد إلكتروني، كود الصنف).
 * تدعم استثناء سجل معين (للتعديل) وتدعم الـ Tenant Scoping لمنع تداخل الشركات.
 */
class Unique implements Rule
{
    protected DatabaseManager $db;
    protected string $table;
    protected string $column;
    protected ?int $exceptId;
    protected string $idColumn;
    protected ?int $companyId;

    /**
     * Unique Rule Constructor.
     *
     * @param DatabaseManager $db
     * @param string $table اسم الجدول (مثال: users)
     * @param string $column اسم العمود (مثال: email)
     * @param int|null $exceptId ID السجل المراد استثناؤه (عند التعديل Update)
     * @param int|null $companyId للتحقق داخل نطاق شركة معينة فقط (Multi-Tenant)
     * @param string $idColumn اسم عمود المعرف (الافتراضي: id)
     */
    public function __construct(
        DatabaseManager $db, 
        string $table, 
        string $column, 
        ?int $exceptId = null, 
        ?int $companyId = null,
        string $idColumn = 'id'
    ) {
        $this->db = $db;
        $this->table = $table;
        $this->column = $column;
        $this->exceptId = $exceptId;
        $this->companyId = $companyId;
        $this->idColumn = $idColumn;
    }

    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true; // دع قاعدة Required تتعامل مع الفراغ
        }

        $sql = "SELECT COUNT(*) as cnt FROM {$this->table} WHERE {$this->column} = ?";
        $bindings = [$value];

        if ($this->exceptId !== null) {
            $sql .= " AND {$this->idColumn} != ?";
            $bindings[] = $this->exceptId;
        }

        if ($this->companyId !== null) {
            $sql .= " AND company_id = ?";
            $bindings[] = $this->companyId;
        }
        
        // استثناء السجلات المحذوفة (Soft Deletes) بافتراض وجود العمود دائماً في الأنظمة المؤسسية
        $sql .= " AND deleted_at IS NULL";

        $result = $this->db->connection()->selectOne($sql, $bindings);

        return (int) $result['cnt'] === 0;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} has already been taken.";
    }
}