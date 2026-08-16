<?php
// Path: app/Core/Calculation/Percentage.php

declare(strict_types=1);

namespace App\Core\Calculation;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Value Object: Percentage
 * يمثل النسبة المئوية بشكل آمن ويمنع أي حسابات خاطئة بسبب نسب سالبة.
 */
class Percentage
{
    public readonly float $value;

    /**
     * Percentage constructor.
     *
     * @param float $value القيمة (مثال: 15 للـ 15%)
     * @throws BusinessException
     */
    public function __construct(float $value)
    {
        if ($value < 0) {
            throw new BusinessException("A percentage value cannot be negative. Provided: {$value}");
        }

        $this->value = $value;
    }

    /**
     * الحصول على القيمة العشرية للنسبة (مثال: 15% تصبح 0.15).
     *
     * @return float
     */
    public function getDecimal(): float
    {
        return $this->value / 100;
    }

    /**
     * حساب قيمة هذه النسبة من مبلغ معين.
     *
     * @param float $amount المبلغ الأساسي
     * @return float
     */
    public function calculateOf(float $amount): float
    {
        return $amount * $this->getDecimal();
    }
}