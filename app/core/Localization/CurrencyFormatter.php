<?php
// Path: app/Core/Localization/CurrencyFormatter.php

declare(strict_types=1);

namespace App\Core\Localization;

use App\Core\Calculation\Money;

/**
 * Enterprise Currency Formatter
 * مسؤول عن طباعة المبالغ المالية بالشكل القانوني والمحاسبي للشركات.
 * يدمج بين (NumberFormatter) وكائن (Money) لإخراج نص نهائي مثل ($ 1,500.00).
 */
class CurrencyFormatter
{
    protected NumberFormatter $numberFormatter;

    /**
     * CurrencyFormatter constructor.
     *
     * @param NumberFormatter $numberFormatter
     */
    public function __construct(NumberFormatter $numberFormatter)
    {
        $this->numberFormatter = $numberFormatter;
    }

    /**
     * فرمتة كائن Money وعرضه مع رمز العملة.
     *
     * @param Money $money
     * @param string $symbol (مثال: $, £, ر.س)
     * @param bool $symbolFirst هل الرمز يكتب قبل أم بعد الرقم؟
     * @param int $decimals
     * @return string
     */
    public function format(Money $money, string $symbol = '', bool $symbolFirst = true, int $decimals = 2): string
    {
        $formattedNumber = $this->numberFormatter->format($money->amount, $decimals);
        $currencyCode = $symbol ?: $money->currency; // إذا لم يمرر الرمز، نستخدم كود العملة (USD)

        if ($symbolFirst) {
            return "{$currencyCode} {$formattedNumber}";
        }

        return "{$formattedNumber} {$currencyCode}";
    }

    /**
     * فرمتة مبلغ خام مع كود العملة.
     *
     * @param float $amount
     * @param string $currencyCode
     * @param int $decimals
     * @return string
     */
    public function formatRaw(float $amount, string $currencyCode, int $decimals = 2): string
    {
        $formattedNumber = $this->numberFormatter->format($amount, $decimals);
        return "{$currencyCode} {$formattedNumber}";
    }
}