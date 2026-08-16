<?php
// Path: app/Domain/Common/Period.php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Domain\Contracts\ValueObjectInterface;
use App\Domain\Exceptions\BusinessRuleViolationException;
use JsonSerializable;

/**
 * Enterprise Value Object: Period
 * يمثل فترة زمنية (من - إلى) ويستخدم في الفترات المالية والإجازات.
 */
class Period implements ValueObjectInterface, JsonSerializable
{
    public function __construct(
        public readonly BusinessDate $start,
        public readonly BusinessDate $end
    ) {
        if ($start->isAfter($end)) {
            throw new BusinessRuleViolationException("Invalid Period: Start date cannot be after end date.");
        }
    }

    public function contains(BusinessDate $date): bool
    {
        return !$date->isBefore($this->start) && !$date->isAfter($this->end);
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->start->equals($other->start) && $this->end->equals($other->end);
    }

    public function jsonSerialize(): array
    {
        return [
            'start_date' => $this->start->formatDb(),
            'end_date'   => $this->end->formatDb(),
        ];
    }
}