<?php
// Path: app/Modules/Accounting/Domain/Entities/JournalEntryLine.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use InvalidArgumentException;

/**
 * Enterprise Domain Entity: Journal Entry Line (سطر القيد المحاسبي)
 * يحتوي على منطق الطرف المدين والدائن لسطر واحد.
 */
class JournalEntryLine
{
    private ?int $id;
    private int $accountId;
    private float $debit;
    private float $credit;
    private ?string $description;
    private ?int $costCenterId;

    public function __construct(
        int $accountId,
        float $debit,
        float $credit,
        ?string $description = null,
        ?int $costCenterId = null,
        ?int $id = null
    ) {
        $this->accountId = $accountId;
        $this->setAmounts($debit, $credit);
        $this->description = $description ? trim($description) : null;
        $this->costCenterId = $costCenterId;
        $this->id = $id;
    }

    private function setAmounts(float $debit, float $credit): void
    {
        $debit = round($debit, 4);
        $credit = round($credit, 4);

        if ($debit < 0 || $credit < 0) {
            throw new InvalidArgumentException("Debit and Credit amounts cannot be negative.");
        }

        if ($debit > 0 && $credit > 0) {
            throw new InvalidArgumentException("A single journal line cannot have both a debit and a credit amount.");
        }

        if ($debit === 0.0 && $credit === 0.0) {
            throw new InvalidArgumentException("A journal line must have either a debit or a credit amount greater than zero.");
        }

        $this->debit = $debit;
        $this->credit = $credit;
    }

    public function getId(): ?int { return $this->id; }
    public function getAccountId(): int { return $this->accountId; }
    public function getDebit(): float { return $this->debit; }
    public function getCredit(): float { return $this->credit; }
    public function getDescription(): ?string { return $this->description; }
    public function getCostCenterId(): ?int { return $this->costCenterId; }
}