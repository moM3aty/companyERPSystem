<?php
// Path: app/Modules/Accounting/Domain/Entities/JournalEntry.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use DomainException;
use InvalidArgumentException;

/**
 * Enterprise Domain Entity: Journal Entry (قيد اليومية)
 * الكيان الأساسي (Aggregate Root) الذي يدير حالة القيد وأسطره.
 */
class JournalEntry
{
    private ?int $id;
    private int $companyId;
    private string $entryNo;
    private string $entryDate;
    private string $status; // 'draft', 'posted', 'voided'
    private string $referenceType;
    private ?string $description;
    
    /** @var JournalEntryLine[] */
    private array $lines = [];

    public function __construct(
        int $companyId,
        string $entryNo,
        string $entryDate,
        string $referenceType = 'Manual',
        ?string $description = null,
        string $status = 'draft',
        ?int $id = null
    ) {
        $this->companyId = $companyId;
        $this->entryNo = trim($entryNo);
        $this->entryDate = $entryDate;
        $this->referenceType = trim($referenceType);
        $this->description = $description ? trim($description) : null;
        $this->status = $status;
        $this->id = $id;
    }

    public function addLine(JournalEntryLine $line): void
    {
        if ($this->status !== 'draft') {
            throw new DomainException("Cannot add lines to a journal entry that is not in draft status.");
        }
        $this->lines[] = $line;
    }

    /**
     * @return JournalEntryLine[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getTotalDebit(): float
    {
        $total = 0.0;
        foreach ($this->lines as $line) {
            $total += $line->getDebit();
        }
        return round($total, 2);
    }

    public function getTotalCredit(): float
    {
        $total = 0.0;
        foreach ($this->lines as $line) {
            $total += $line->getCredit();
        }
        return round($total, 2);
    }

    public function isBalanced(): bool
    {
        return $this->getTotalDebit() === $this->getTotalCredit();
    }

    public function post(): void
    {
        if ($this->status === 'posted') {
            throw new DomainException("Journal entry is already posted.");
        }

        if ($this->status === 'voided') {
            throw new DomainException("Cannot post a voided journal entry.");
        }

        if (count($this->lines) < 2) {
            throw new DomainException("Journal entry must have at least two lines to be posted.");
        }

        if (!$this->isBalanced()) {
            throw new DomainException(sprintf(
                "Cannot post unbalanced journal entry. Total Debit: %s, Total Credit: %s",
                $this->getTotalDebit(),
                $this->getTotalCredit()
            ));
        }

        if ($this->getTotalDebit() <= 0) {
            throw new DomainException("Journal entry total amount must be greater than zero.");
        }

        $this->status = 'posted';
    }

    public function voidEntry(): void
    {
        if ($this->status === 'voided') {
            throw new DomainException("Journal entry is already voided.");
        }
        
        $this->status = 'voided';
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getEntryNo(): string { return $this->entryNo; }
    public function getEntryDate(): string { return $this->entryDate; }
    public function getStatus(): string { return $this->status; }
    public function getReferenceType(): string { return $this->referenceType; }
    public function getDescription(): ?string { return $this->description; }
}