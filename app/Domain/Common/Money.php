<?php
// Path: app/Domain/Common/Money.php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Domain\Contracts\ValueObjectInterface;
use App\Domain\Exceptions\BusinessRuleViolationException;
use JsonSerializable;

/**
 * Enterprise Value Object: Money
 * يمثل المبالغ المالية. الكائن Immutable (لا يتغير)، أي عملية حسابية تنتج كائناً جديداً.
 */
class Money implements ValueObjectInterface, JsonSerializable
{
    public readonly float $amount;
    public readonly string $currency;

    public function __construct(float $amount, string $currency)
    {
        // التقريب المحاسبي لضمان الدقة المزدوجة
        $this->amount = round($amount, 4); 
        $this->currency = strtoupper(trim($currency));
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function equals(ValueObjectInterface $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    protected function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new BusinessRuleViolationException("Currency Mismatch: Cannot perform operations between {$this->currency} and {$other->currency}.");
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'formatted' => "{$this->currency} " . number_format($this->amount, 2)
        ];
    }
}