<?php
// Path: app/Domain/Common/Quantity.php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Domain\Contracts\ValueObjectInterface;
use App\Domain\Exceptions\BusinessRuleViolationException;
use JsonSerializable;

/**
 * Enterprise Value Object: Quantity
 * يدير الكميات في المخازن بدقة ويمنع دمج وحدات قياس مختلفة (مثال: كيلوجرام مع حبة).
 */
class Quantity implements ValueObjectInterface, JsonSerializable
{
    public readonly float $value;
    public readonly string $unit;

    public function __construct(float $value, string $unit = 'PCS')
    {
        $this->value = round($value, 4);
        $this->unit = strtoupper(trim($unit));
    }

    public function add(self $other): self
    {
        $this->assertSameUnit($other);
        return new self($this->value + $other->value, $this->unit);
    }

    public function subtract(self $other): self
    {
        $this->assertSameUnit($other);
        
        if (($this->value - $other->value) < 0) {
            throw new BusinessRuleViolationException("Inventory Error: Quantity cannot fall below zero.");
        }

        return new self($this->value - $other->value, $this->unit);
    }

    public function equals(ValueObjectInterface $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        return $this->value === $other->value && $this->unit === $other->unit;
    }

    protected function assertSameUnit(self $other): void
    {
        if ($this->unit !== $other->unit) {
            throw new BusinessRuleViolationException("UOM Mismatch: Cannot operate between {$this->unit} and {$other->unit}.");
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'unit' => $this->unit,
        ];
    }
}