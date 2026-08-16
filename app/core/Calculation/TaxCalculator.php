<?php
// Path: app/Core/Calculation/TaxCalculator.php

declare(strict_types=1);

namespace App\Core\Calculation;

/**
 * Enterprise Tax Calculator
 * يحسب الضرائب بدقة، ويدعم النظامين (السعر شامل الضريبة / السعر غير شامل الضريبة).
 */
class TaxCalculator
{
    /**
     * حساب قيمة الضريبة إذا كان السعر "غير شامل الضريبة" (Exclusive).
     * مثال: السعر 100، الضريبة 15% -> النتيجة 15.
     *
     * @param float $amount السعر الأساسي
     * @param Percentage $taxRate نسبة الضريبة
     * @return float قيمة الضريبة فقط
     */
    public static function calculateExclusiveTax(float $amount, Percentage $taxRate): float
    {
        $taxAmount = $taxRate->calculateOf($amount);
        
        return RoundingService::roundFinancial($taxAmount);
    }

    /**
     * حساب قيمة الضريبة إذا كان السعر "شاملاً للضريبة" (Inclusive).
     * مثال: السعر الشامل 115، الضريبة 15% -> النتيجة 15 (والمبلغ الأساسي 100).
     * المعادلة: Tax = Total - (Total / (1 + Rate))
     *
     * @param float $inclusiveAmount المبلغ الشامل للضريبة
     * @param Percentage $taxRate نسبة الضريبة
     * @return float قيمة الضريبة المستقطعة من المبلغ الشامل
     */
    public static function extractTaxFromInclusive(float $inclusiveAmount, Percentage $taxRate): float
    {
        $rateDecimal = $taxRate->getDecimal();
        
        if ($rateDecimal === 0.0) {
            return 0.0;
        }

        $baseAmount = $inclusiveAmount / (1 + $rateDecimal);
        $taxAmount = $inclusiveAmount - $baseAmount;

        return RoundingService::roundFinancial($taxAmount);
    }

    /**
     * الحصول على المبلغ الأساسي من مبلغ شامل الضريبة.
     *
     * @param float $inclusiveAmount
     * @param Percentage $taxRate
     * @return float
     */
    public static function getBaseFromInclusive(float $inclusiveAmount, Percentage $taxRate): float
    {
        $tax = self::extractTaxFromInclusive($inclusiveAmount, $taxRate);
        return RoundingService::roundFinancial($inclusiveAmount - $tax);
    }
}