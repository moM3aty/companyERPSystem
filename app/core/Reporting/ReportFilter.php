<?php
// Path: app/Core/Reporting/ReportFilter.php

declare(strict_types=1);

namespace App\Core\Reporting;

/**
 * Enterprise Report Filter DTO
 * يغلف الفلاتر المرسلة من واجهة المستخدم ليتم تطبيقها بأمان على استعلام الـ DataSet.
 */
class ReportFilter
{
    public readonly string $column;
    public readonly string $operator;
    public readonly mixed $value;

    /**
     * ReportFilter constructor.
     *
     * @param string $column
     * @param string $operator (=, >, <, >=, <=, LIKE, IN, BETWEEN)
     * @param mixed $value
     */
    public function __construct(string $column, string $operator, mixed $value)
    {
        $this->column = $column;
        $this->operator = strtoupper(trim($operator));
        $this->value = $value;
    }

    /**
     * التحقق من أمان المعامل (Operator) لمنع الـ SQL Injection الخفي.
     *
     * @return bool
     */
    public function isValidOperator(): bool
    {
        $allowed = ['=', '>', '<', '>=', '<=', '!=', '<>', 'LIKE', 'IN', 'BETWEEN', 'IS NULL', 'IS NOT NULL'];
        return in_array($this->operator, $allowed, true);
    }
}