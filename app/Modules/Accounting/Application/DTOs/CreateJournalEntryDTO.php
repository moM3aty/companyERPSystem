<?php
// Path: app/Modules/Accounting/Application/DTOs/CreateJournalEntryDTO.php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\DTOs;

use InvalidArgumentException;

/**
 * Enterprise DTO: Create Journal Entry
 * ينقل الرأس (Header) والأسطر (Lines) معاً لضمان إنشاء القيد كوحدة واحدة (Atomic).
 */
class CreateJournalEntryDTO
{
    /**
     * @param int $companyId
     * @param int $userId
     * @param string $entryDate
     * @param string $description
     * @param JournalEntryLineDTO[] $lines
     * @param int|null $branchId
     * @param string|null $referenceType
     * @param int|null $referenceId
     * @param int|null $currencyId
     * @param float $exchangeRate
     */
    public function __construct(
        public readonly int $companyId,
        public readonly int $userId,
        public readonly string $entryDate,
        public readonly string $description,
        public readonly array $lines,
        public readonly ?int $branchId = null,
        public readonly ?string $referenceType = null,
        public readonly ?int $referenceId = null,
        public readonly ?int $currencyId = null,
        public readonly float $exchangeRate = 1.000000
    ) {
        if (empty($this->lines) || count($this->lines) < 2) {
            throw new InvalidArgumentException("Journal entry must have at least two lines.");
        }

        foreach ($this->lines as $line) {
            if (!$line instanceof JournalEntryLineDTO) {
                throw new InvalidArgumentException("All lines must be instances of JournalEntryLineDTO.");
            }
        }
    }
}