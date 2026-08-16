<?php
// Path: app/Modules/Accounting/Domain/Entities/FiscalPeriod.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;

/**
 * Enterprise Domain Entity: Fiscal Period (الفترة المالية)
 * يمثل شهر مالي أو ربع أو سنة ويتحكم في إغلاق الفترات لمنع التوجيه المحاسبي.
 */
class FiscalPeriod
{
    private ?int $id;
    private int $companyId;
    private string $periodName;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;
    private string $status; // 'open', 'closed', 'locked'

    public function __construct(
        int $companyId,
        string $periodName,
        string $startDate,
        string $endDate,
        string $status = 'open',
        ?int $id = null
    ) {
        $this->companyId = $companyId;
        $this->periodName = trim($periodName);
        $this->startDate = new DateTimeImmutable($startDate);
        $this->endDate = new DateTimeImmutable($endDate);
        
        if ($this->startDate >= $this->endDate) {
            throw new InvalidArgumentException("Fiscal period start date must be strictly before end date.");
        }

        $validStatuses = ['open', 'closed', 'locked'];
        if (!in_array($status, $validStatuses, true)) {
            throw new InvalidArgumentException("Status must be one of: " . implode(', ', $validStatuses));
        }
        $this->status = $status;
        $this->id = $id;
    }

    public function close(): void
    {
        if ($this->status === 'locked') {
            throw new DomainException("Cannot close a period that is already permanently locked.");
        }
        $this->status = 'closed';
    }

    public function reopen(): void
    {
        if ($this->status === 'locked') {
            throw new DomainException("Cannot reopen a permanently locked fiscal period. (Audit constraint).");
        }
        $this->status = 'open';
    }

    public function lockPermanently(): void
    {
        $this->status = 'locked';
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function containsDate(string $date): bool
    {
        $targetDate = new DateTimeImmutable($date);
        return $targetDate >= $this->startDate && $targetDate <= $this->endDate;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getPeriodName(): string { return $this->periodName; }
    public function getStartDate(): DateTimeImmutable { return $this->startDate; }
    public function getEndDate(): DateTimeImmutable { return $this->endDate; }
    public function getStatus(): string { return $this->status; }
}