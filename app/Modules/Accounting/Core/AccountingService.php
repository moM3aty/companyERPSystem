<?php
// Path: app/Modules/Accounting/Core/AccountingService.php

declare(strict_types=1);

namespace App\Modules\Accounting\Core;

/**
 * Enterprise Domain Service: Accounting Service
 * Orchestrator for complex accounting workflows like Year-End Closing, 
 * P&L generation, and Trial Balance compilation.
 */
class AccountingService
{
    private FinancialCalculationService $calcService;

    public function __construct(FinancialCalculationService $calcService)
    {
        $this->calcService = $calcService;
    }

    /**
     * Process raw ledger lines into a formatted Trial Balance.
     * 
     * @param array $rawLedgerData Data from AccountRepository joining JournalEntryLines
     * @return array
     */
    public function compileTrialBalance(array $rawLedgerData): array
    {
        $trialBalance = [];
        $totalDebit = 0.00;
        $totalCredit = 0.00;

        foreach ($rawLedgerData as $row) {
            $code = $row['account_code'];
            $name = $row['account_name'];
            $normalBalance = $row['normal_balance']; // 'Debit' or 'Credit'
            
            $sumDebit = (float) $row['sum_debit'];
            $sumCredit = (float) $row['sum_credit'];

            // Net the balance
            $netBalance = $this->calcService->calculateAccountBalance($sumDebit, $sumCredit, $normalBalance);

            // Determine presentation side
            $finalDebit = 0.00;
            $finalCredit = 0.00;

            if ($netBalance > 0) {
                if ($normalBalance === 'Debit') {
                    $finalDebit = $netBalance;
                } else {
                    $finalCredit = $netBalance;
                }
            } elseif ($netBalance < 0) {
                // Contra-account behavior (e.g. Asset with a Credit balance)
                if ($normalBalance === 'Debit') {
                    $finalCredit = abs($netBalance);
                } else {
                    $finalDebit = abs($netBalance);
                }
            }

            if ($finalDebit > 0 || $finalCredit > 0) {
                $trialBalance[] = [
                    'account_code' => $code,
                    'account_name' => $name,
                    'debit'        => $finalDebit,
                    'credit'       => $finalCredit
                ];

                $totalDebit += $finalDebit;
                $totalCredit += $finalCredit;
            }
        }

        return [
            'lines' => $trialBalance,
            'totals' => [
                'debit'  => round($totalDebit, 2),
                'credit' => round($totalCredit, 2),
                'is_balanced' => round($totalDebit, 2) === round($totalCredit, 2)
            ]
        ];
    }
}