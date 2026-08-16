<?php
// Path: app/Modules/Accounting/Core/JournalPostingService.php

declare(strict_types=1);

namespace App\Modules\Accounting\Core;

use Exception;
use RuntimeException;

/**
 * Enterprise Domain Service: Journal Posting Service
 * Enforces accounting principles (Double-Entry, Balanced Ledger) 
 * before allowing any journal entry to be posted.
 */
class JournalPostingService
{
    private AccountingPeriodService $periodService;
    private FinancialCalculationService $calcService;

    public function __construct(
        AccountingPeriodService $periodService,
        FinancialCalculationService $calcService
    ) {
        $this->periodService = $periodService;
        $this->calcService = $calcService;
    }

    /**
     * Validate a Journal Entry before persistence or posting.
     *
     * @param int $companyId
     * @param string $entryDate
     * @param array $lines
     * @return void
     * @throws RuntimeException
     */
    public function validateEntry(int $companyId, string $entryDate, array $lines): void
    {
        // 1. Validate lines exist
        if (empty($lines) || count($lines) < 2) {
            throw new RuntimeException("A valid journal entry requires at least two lines (one debit and one credit).");
        }

        // 2. Validate Fiscal Period is Open
        $this->periodService->validateDateIsOpen($companyId, $entryDate);

        // 3. Validate Debit = Credit
        if (!$this->calcService->isBalanced($lines)) {
            $totalDebit = array_sum(array_column($lines, 'debit'));
            $totalCredit = array_sum(array_column($lines, 'credit'));
            throw new RuntimeException(sprintf(
                "Unbalanced Entry: Total Debits (%.2f) do not equal Total Credits (%.2f).",
                $totalDebit,
                $totalCredit
            ));
        }

        // 4. Validate all amounts are positive
        foreach ($lines as $line) {
            if (($line['debit'] ?? 0) < 0 || ($line['credit'] ?? 0) < 0) {
                throw new RuntimeException("Journal Entry lines cannot contain negative values. Swap debit and credit instead.");
            }
        }
    }

    /**
     * Verify if a journal entry can be safely voided.
     */
    public function canBeVoided(string $currentStatus, string $entryDate, int $companyId): bool
    {
        if ($currentStatus === 'voided') {
            throw new RuntimeException("Journal Entry is already voided.");
        }

        // Prevent voiding if period is closed
        $this->periodService->validateDateIsOpen($companyId, $entryDate);

        return true;
    }
}