<?php
// Path: app/Domain/Common/Percentage.php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Domain\Contracts\ValueObjectInterface;
use App\Domain\Exceptions\BusinessRuleViolationException;
use JsonSerializable;

/**
 * Enterprise Value Object: Percentage
 * يحمي النظام من النسب الخاطئة (السالبة) لتأمين حسابات الضرائب والخصومات.
 */
class Percentage implements ValueObjectInterface, JsonSerializable
{
    public readonly float $value;

    public function __construct(float $value)
    {
        if ($value < 0) {
            throw new BusinessRuleViolationException("A percentage value cannot be negative. Provided: {$value}");
        }
        $this->value = round($value, 2);
    }

    public function getDecimal(): float
    {
        return $this->value / 100;
    }

    public function calculateOf(float $amount): float
    {
        return $amount * $this->getDecimal();
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function jsonSerialize(): float
    {
        return $this->value;
    }
}