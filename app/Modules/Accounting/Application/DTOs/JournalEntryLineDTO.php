<?php
// Path: app/Modules/Accounting/Application/DTOs/JournalEntryLineDTO.php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs;

use InvalidArgumentException;

/**
 * Enterprise DTO: Journal Entry Line
 * يمثل سطر واحد (مدين أو دائن) داخل القيد المحاسبي.
 */
class JournalEntryLineDTO
{
    public function __construct(
        public readonly int $accountId,
        public readonly float $debit,
        public readonly float $credit,
        public readonly ?string $description = null,
        public readonly ?int $costCenterId = null,
        public readonly ?int $departmentId = null,
        public readonly ?int $projectId = null
    ) {
        if ($this->debit < 0 || $this->credit < 0) {
            throw new InvalidArgumentException("Debit and Credit amounts cannot be negative.");
        }

        if ($this->debit > 0 && $this->credit > 0) {
            throw new InvalidArgumentException("A single line cannot have both Debit and Credit amounts. Must be one or the other.");
        }

        if ($this->debit === 0.0 && $this->credit === 0.0) {
            throw new InvalidArgumentException("Line must have either a Debit or a Credit amount greater than zero.");
        }
    }
}