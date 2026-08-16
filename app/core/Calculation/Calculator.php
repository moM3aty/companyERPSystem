<?php
// Path: app/Core/Calculation/Calculator.php

declare(strict_types=1);

namespace App\Core\Calculation;

/**
 * Enterprise Calculator Facade
 * واجهة تجميعية (Facade) تسهل على مطوري النظام استخدام جميع أدوات الحساب بأسلوب نظيف وسريع.
 */
class Calculator
{
    public static function money(float $amount, string $currency): Money
    {
        return new Money($amount, $currency);
    }

    public static function percentage(float $value): Percentage
    {
        return new Percentage($value);
    }

    public static function round(float $amount, int $precision = 2): float
    {
        return RoundingService::roundFinancial($amount, $precision);
    }

    public static function convert(Money $money, string $targetCurrency, float $rate): Money
    {
        return CurrencyConverter::convert($money, $targetCurrency, $rate);
    }

    public static function lineItem(
        float $quantity, 
        float $unitPrice, 
        float $discountAmount = 0.0, 
        float $taxRate = 0.0, 
        bool $isInclusive = false
    ): array {
        return TotalCalculator::calculateLineItem(
            $quantity,
            $unitPrice,
            $discountAmount,
            new Percentage($taxRate),
            $isInclusive
        );
    }
}