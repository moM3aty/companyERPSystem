<?php
// Path: app/Modules/Accounting/Core/FinancialCalculationService.php

declare(strict_types=1);

namespace App\Modules\Accounting\Core;

/**
 * Enterprise Domain Service: Financial Calculation Service
 * Contains pure mathematical and financial calculation logic.
 * Designed to be highly testable without database dependencies.
 */
class FinancialCalculationService
{
    /**
     * Calculate the correct balance based on the account's normal balance type.
     *
     * @param float $totalDebit
     * @param float $totalCredit
     * @param string $normalBalance 'Debit' or 'Credit'
     * @return float
     */
    public function calculateAccountBalance(float $totalDebit, float $totalCredit, string $normalBalance): float
    {
        if ($normalBalance === 'Debit') {
            return $totalDebit - $totalCredit;
        }

        return $totalCredit - $totalDebit;
    }

    /**
     * Check if the given lines (debits and credits) are perfectly balanced.
     * 
     * @param array $lines Array containing 'debit' and 'credit' keys.
     * @param int $precision
     * @return bool
     */
    public function isBalanced(array $lines, int $precision = 2): bool
    {
        $totalDebit = 0.00;
        $totalCredit = 0.00;

        foreach ($lines as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }

        // Use round to prevent floating point precision issues (e.g. 0.1 + 0.2 != 0.3 in PHP)
        return round($totalDebit, $precision) === round($totalCredit, $precision);
    }

    /**
     * Calculate the base currency amount based on exchange rate.
     */
    public function convertToBaseCurrency(float $amount, float $exchangeRate): float
    {
        if ($exchangeRate <= 0) {
            return $amount;
        }
        
        return round($amount * $exchangeRate, 4);
    }
}