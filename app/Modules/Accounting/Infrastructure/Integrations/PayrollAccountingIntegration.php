<?php
// Path: app/Modules/Accounting/Infrastructure/Integrations/PayrollAccountingIntegration.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Integrations;

use App\Modules\Accounting\Application\Services\JournalEntryService;
use App\Modules\Accounting\Application\DTOs\CreateJournalEntryDTO;
use App\Modules\Accounting\Application\DTOs\JournalEntryLineDTO;

class PayrollAccountingIntegration
{
    public function __construct(private JournalEntryService $journalService) {}

    public function postPayrollRun(int $companyId, int $userId, array $payrollData): int
    {
        /*
         * Standard Payroll Entry:
         * DR Salary Expense (Gross)
         * CR Salaries Payable (Net)
         * CR GOSI/Tax Payable (Deductions)
         */
        $lines = [
            new JournalEntryLineDTO(
                accountId: $payrollData['expense_account_id'],
                debit: $payrollData['gross_salary'],
                credit: 0.00,
                description: "Gross Salary Expense for " . $payrollData['period']
            ),
            new JournalEntryLineDTO(
                accountId: $payrollData['payable_account_id'],
                debit: 0.00,
                credit: $payrollData['net_salary'],
                description: "Net Salary Payable for " . $payrollData['period']
            )
        ];

        if (($payrollData['deductions'] ?? 0) > 0) {
            $lines[] = new JournalEntryLineDTO(
                accountId: $payrollData['deductions_account_id'],
                debit: 0.00,
                credit: $payrollData['deductions'],
                description: "Statutory Deductions for " . $payrollData['period']
            );
        }

        $dto = new CreateJournalEntryDTO(
            companyId: $companyId,
            userId: $userId,
            entryDate: $payrollData['run_date'],
            description: "Automated Payroll Posting: " . $payrollData['period'],
            lines: $lines,
            referenceType: 'Payroll Run',
            referenceId: $payrollData['run_id']
        );

        return $this->journalService->createAndPostEntry($dto, true);
    }
}