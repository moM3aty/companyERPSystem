<?php
// Path: app/Core/Calculation/CurrencyConverter.php

declare(strict_types=1);

namespace App\Core\Calculation;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Currency Converter
 * يحول كائنات الـ Money من عملة لأخرى بناءً على معامل الصرف (Exchange Rate).
 */
class CurrencyConverter
{
    /**
     * تحويل مبلغ من عملة لأخرى.
     *
     * @param Money $money المبلغ والعملة الأصلية
     * @param string $targetCurrency العملة المستهدفة
     * @param float $exchangeRate معامل الصرف
     * @return Money كائن Money جديد بالعملة المستهدفة
     * @throws BusinessException
     */
    public static function convert(Money $money, string $targetCurrency, float $exchangeRate): Money
    {
        $targetCurrency = strtoupper(trim($targetCurrency));

        if ($exchangeRate <= 0.0) {
            throw new BusinessException("Exchange rate must be greater than zero.");
        }

        if ($money->currency === $targetCurrency) {
            return $money; // لا حاجة للتحويل
        }

        // تحويل القيمة وتقريبها
        $convertedAmount = RoundingService::roundFinancial($money->amount * $exchangeRate);

        return new Money($convertedAmount, $targetCurrency);
    }
}