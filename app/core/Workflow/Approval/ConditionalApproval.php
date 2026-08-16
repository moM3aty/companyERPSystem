<?php
// Path: app/Core/Workflow/Approval/ConditionalApproval.php

declare(strict_types=1);

namespace App\Core\Workflow\Approval;

/**
 * Enterprise Conditional Approval Evaluator
 * يقرأ الشروط المخزنة في الداتابيز كـ JSON ويطبقها على البيانات الفعلية (Payload).
 */
class ConditionalApproval
{
    /**
     * تقييم قائمة الشروط (AND Logic).
     *
     * @param array $conditions [['field' => 'amount', 'operator' => '>', 'value' => 10000]]
     * @param array $payload ['amount' => 15000, 'department' => 'IT']
     * @return bool
     */
    public function evaluate(array $conditions, array $payload): bool
    {
        if (empty($conditions)) {
            return true; // لا توجد شروط، إذن ينطبق دائماً
        }

        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? '';
            $operator = $condition['operator'] ?? '=';
            $expectedValue = $condition['value'] ?? null;
            
            $actualValue = $payload[$field] ?? null;

            if (!$this->compare($actualValue, $operator, $expectedValue)) {
                return false; // بمجرد فشل شرط واحد نرفض المستوى (AND Logic)
            }
        }

        return true;
    }

    protected function compare(mixed $actual, string $operator, mixed $expected): bool
    {
        // توحيد الأنواع للأرقام لضمان الدقة
        if (is_numeric($actual) && is_numeric($expected)) {
            $actual = (float) $actual;
            $expected = (float) $expected;
        }

        return match ($operator) {
            '='  => $actual == $expected,
            '!=' => $actual != $expected,
            '>'  => $actual > $expected,
            '<'  => $actual < $expected,
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            'IN' => is_array($expected) && in_array($actual, $expected, true),
            default => false,
        };
    }
}