<?php
// Path: app/Core/Workflow/WorkflowCondition.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Workflow Condition Evaluator
 * يقوم بتقييم ما إذا كان مسموحاً بعبور المسار (Transition) بناءً على الشروط المخزنة.
 */
class WorkflowCondition
{
    /**
     * تقييم شرط معين.
     *
     * @param string $operator ('=', '>', '<', '>=', '<=', '!=')
     * @param mixed $expectedValue
     * @param mixed $actualValue
     * @return bool
     * @throws BusinessException
     */
    public function evaluate(string $operator, mixed $expectedValue, mixed $actualValue): bool
    {
        return match ($operator) {
            '=' => $actualValue == $expectedValue,
            '!=' => $actualValue != $expectedValue,
            '>' => $actualValue > $expectedValue,
            '<' => $actualValue < $expectedValue,
            '>=' => $actualValue >= $expectedValue,
            '<=' => $actualValue <= $expectedValue,
            default => throw new BusinessException("Unknown condition operator: {$operator}"),
        };
    }
}