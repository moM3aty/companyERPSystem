<?php
// Path: app/Core/Reporting/DataSet.php

declare(strict_types=1);

namespace App\Core\Reporting;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\DatabaseException;
use PDO;

/**
 * Enterprise Data Set Engine
 * يتولى مسؤولية استخراج البيانات من قاعدة البيانات بناءً على هيكل التقرير (Definition)
 * مع تطبيق الفلاتر بشكل آمن تماماً (Prepared Statements) لمنع الـ SQL Injection.
 */
class DataSet
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * سحب بيانات التقرير كـ Generator لتقليل استهلاك الذاكرة (Memory Usage) عند تصدير ملايين السجلات.
     *
     * @param ReportDefinition $definition
     * @param array<ReportFilter> $filters
     * @param int $companyId
     * @return \Generator
     * @throws BusinessException|DatabaseException
     */
    public function fetch(ReportDefinition $definition, array $filters, int $companyId): \Generator
    {
        $baseQuery = $definition->getAttribute('base_query');
        
        if (empty($baseQuery)) {
            throw new BusinessException("Report definition is missing a base query.");
        }

        // بناء الـ WHERE Clause بأمان
        $whereClauses = ["company_id = ?"];
        $bindings = [$companyId];

        foreach ($filters as $filter) {
            if (!$filter instanceof ReportFilter || !$filter->isValidOperator()) {
                throw new BusinessException("Invalid report filter provided.");
            }

            if ($filter->operator === 'IN' && is_array($filter->value)) {
                $placeholders = implode(',', array_fill(0, count($filter->value), '?'));
                $whereClauses[] = "{$filter->column} IN ({$placeholders})";
                $bindings = array_merge($bindings, array_values($filter->value));
            } elseif ($filter->operator === 'BETWEEN' && is_array($filter->value) && count($filter->value) === 2) {
                $whereClauses[] = "{$filter->column} BETWEEN ? AND ?";
                $bindings[] = $filter->value[0];
                $bindings[] = $filter->value[1];
            } elseif (in_array($filter->operator, ['IS NULL', 'IS NOT NULL'])) {
                $whereClauses[] = "{$filter->column} {$filter->operator}";
            } else {
                $whereClauses[] = "{$filter->column} {$filter->operator} ?";
                $bindings[] = $filter->value;
            }
        }

        $whereSql = implode(' AND ', $whereClauses);
        
        // التحقق من وجود كلمة WHERE في الاستعلام الأساسي لمعرفة كيفية دمج الفلاتر
        if (stripos($baseQuery, 'WHERE') !== false) {
            $finalQuery = str_ireplace('WHERE', "WHERE {$whereSql} AND", $baseQuery);
        } else {
            // إضافة الـ WHERE قبل الـ ORDER BY أو GROUP BY إن وُجدت
            $finalQuery = $baseQuery;
            if (preg_match('/(GROUP BY|ORDER BY|LIMIT)/i', $finalQuery, $matches, PREG_OFFSET_CAPTURE)) {
                $position = $matches[0][1];
                $finalQuery = substr_replace($finalQuery, " WHERE {$whereSql} ", $position, 0);
            } else {
                $finalQuery .= " WHERE {$whereSql}";
            }
        }

        // تنفيذ الاستعلام وإرجاع النتائج كـ Generator (Yield) لمنع امتلاء رامات السيرفر
        $pdo = $this->db->connection()->getPdo();
        $stmt = $pdo->prepare($finalQuery);
        $stmt->execute($bindings);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }
}