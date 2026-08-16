<?php
// Path: app/Modules/Accounting/Domain/ValueObjects/Money.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Enterprise Value Object: Money
 * يضمن عدم وجود مبالغ مالية سالبة بالخطأ، ويثبت العملة المستخدمة.
 * (Immutable Object - لا يمكن تغيير قيمته بعد إنشائه)
 */
final class Money
{
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'SAR')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException("Money amount cannot be negative.");
        }

        $this->amount = round($amount, 4); // دقة 4 أصفار عشرية للعمليات المحاسبية
        $this->currency = strtoupper($currency);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->getAmount(), $this->currency);
    }

    public function subtract(Money $other): self
    {
        $this->assertSameCurrency($other);
        $newAmount = $this->amount - $other->getAmount();
        
        if ($newAmount < 0) {
            throw new InvalidArgumentException("Subtraction results in negative money.");
        }

        return new self($newAmount, $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->getAmount() && $this->currency === $other->getCurrency();
    }

    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidArgumentException("Cannot operate on different currencies without exchange rate calculation.");
        }
    }
}