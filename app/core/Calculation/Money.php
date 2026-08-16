<?php
// Path: app/Core/Calculation/Money.php

declare(strict_types=1);

namespace App\Core\Calculation;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Value Object: Money
 * يمنع تماماً الكوارث المحاسبية الناتجة عن جمع وطرح عملات مختلفة بدون تحويل!
 * الكائن غير قابل للتغيير (Immutable)، أي عملية حسابية تُرجع كائن Money جديد.
 */
class Money
{
    public readonly float $amount;
    public readonly string $currency;

    /**
     * Money constructor.
     *
     * @param float $amount
     * @param string $currency (مثال: USD, SAR, EGP)
     */
    public function __construct(float $amount, string $currency)
    {
        $this->amount = RoundingService::roundFinancial($amount);
        $this->currency = strtoupper(trim($currency));
    }

    /**
     * جمع مبلغين.
     *
     * @param Money $other
     * @return Money
     * @throws BusinessException
     */
    public function add(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * طرح مبلغين.
     *
     * @param Money $other
     * @return Money
     * @throws BusinessException
     */
    public function subtract(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    /**
     * ضرب المبلغ في معامل.
     *
     * @param float $multiplier
     * @return Money
     */
    public function multiply(float $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    /**
     * قسمة المبلغ.
     *
     * @param float $divisor
     * @return Money
     * @throws BusinessException
     */
    public function divide(float $divisor): self
    {
        if ($divisor === 0.0) {
            throw new BusinessException("Cannot divide money by zero.");
        }
        return new self($this->amount / $divisor, $this->currency);
    }

    /**
     * التأكد من أن العملة متطابقة قبل الحساب.
     *
     * @param Money $other
     * @return void
     * @throws BusinessException
     */
    protected function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new BusinessException("Currency mismatch: Cannot perform arithmetic operations between {$this->currency} and {$other->currency} without conversion.");
        }
    }
}