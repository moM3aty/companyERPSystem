<?php
// Path: app/Core/Calculation/DiscountCalculator.php

declare(strict_types=1);

namespace App\Core\Calculation;

/**
 * Enterprise Discount Calculator
 * محرك احتساب الخصومات التجارية مع ضمان عدم تجاوز الخصم لقيمة المنتج.
 */
class DiscountCalculator
{
    /**
     * حساب مبلغ الخصم بناءً على نسبة مئوية.
     *
     * @param float $amount المبلغ الأساسي
     * @param Percentage $discountPercentage نسبة الخصم
     * @return float قيمة الخصم كأصل مالي
     */
    public static function calculatePercentageDiscount(float $amount, Percentage $discountPercentage): float
    {
        // نسبة الخصم لا يمكن أن تتجاوز 100%
        $rate = min(100.0, $discountPercentage->value);
        $safePercentage = new Percentage($rate);

        $discountValue = $safePercentage->calculateOf($amount);

        return RoundingService::roundFinancial($discountValue);
    }

    /**
     * تطبيق مبلغ خصم ثابت وضمان عدم تجاوز الإجمالي (لا توجد مبالغ سالبة).
     *
     * @param float $amount المبلغ الأساسي
     * @param float $discountAmount مبلغ الخصم
     * @return float المبلغ المتبقي بعد الخصم
     */
    public static function applyFixedDiscount(float $amount, float $discountAmount): float
    {
        // نضمن أن الخصم لا يتجاوز المبلغ الأساسي وأن لا يكون سالباً
        $validDiscount = max(0.0, min($amount, $discountAmount));

        return RoundingService::roundFinancial($amount - $validDiscount);
    }
}