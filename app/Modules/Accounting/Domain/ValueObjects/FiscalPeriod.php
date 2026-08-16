<?php
// Path: app/Modules/Accounting/Domain/ValueObjects/FiscalPeriod.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Enterprise Value Object: Fiscal Period
 * يتحقق من منطقية تواريخ البداية والنهاية للفترات المالية.
 */
final class FiscalPeriod
{
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = new DateTimeImmutable($startDate);
        $this->endDate = new DateTimeImmutable($endDate);

        if ($this->startDate >= $this->endDate) {
            throw new InvalidArgumentException("Fiscal period start date must be strictly before end date.");
        }
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function contains(DateTimeImmutable $date): bool
    {
        return $date >= $this->startDate && $date <= $this->endDate;
    }
}