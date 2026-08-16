<?php
// Path: app/Core/Finance/Services/AccountingService.php

declare(strict_types=1);

namespace App\Core\Finance\Services;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Finance\Repositories\ChartOfAccountsRepository;
use App\Core\Finance\Repositories\JournalEntryRepository;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Accounting Service
 * The Financial Brain. Automates the creation of Journal Entries from operational documents (like Invoices).
 * Enforces strict Double-Entry Bookkeeping rules (Total Debits MUST equal Total Credits).
 */
class AccountingService
{
    protected JournalEntryRepository $journalRepo;
    protected ChartOfAccountsRepository $coaRepo;
    protected TransactionManager $transaction;
    protected TenantContext $tenantContext;

    /**
     * AccountingService constructor.
     *
     * @param JournalEntryRepository $journalRepo
     * @param ChartOfAccountsRepository $coaRepo
     * @param TransactionManager $transaction
     * @param TenantContext $tenantContext
     */
    public function __construct(
        JournalEntryRepository $journalRepo,
        ChartOfAccountsRepository $coaRepo,
        TransactionManager $transaction,
        TenantContext $tenantContext
    ) {
        $this->journalRepo = $journalRepo;
        $this->coaRepo = $coaRepo;
        $this->transaction = $transaction;
        $this->tenantContext = $tenantContext;
    }

    /**
     * Create a generalized Journal Entry and ensure it is mathematically balanced.
     *
     * @param array $headerData
     * @param array $linesData
     * @param int $userId
     * @return int The ID of the newly created Journal Entry
     * @throws BusinessException|\Throwable
     */
    public function createJournalEntry(array $headerData, array $linesData, int $userId): int
    {
        $companyId = $this->tenantContext->requireTenant()->companyId;
        $branchId = $this->tenantContext->getBranchId();

        if (empty($linesData)) {
            throw new BusinessException("A journal entry must contain at least two lines (Debit and Credit).", 422);
        }

        // 1. Validate Double-Entry Rule (Debits = Credits)
        $totalDebit = 0.0;
        $totalCredit = 0.0;
        $processedLines = [];

        foreach ($linesData as $line) {
            $debit = round((float) ($line['debit'] ?? 0.0), 2);
            $credit = round((float) ($line['credit'] ?? 0.0), 2);

            if ($debit == 0.0 && $credit == 0.0) {
                continue; // Skip empty lines
            }

            if ($debit > 0 && $credit > 0) {
                throw new BusinessException("A single journal entry line cannot have both a debit and a credit value.", 422);
            }

            // Verify account validity (Not a control account, active, etc.)
            $accountId = (int) $line['account_id'];
            $this->coaRepo->getValidPostingAccount($accountId, $companyId);

            $totalDebit += $debit;
            $totalCredit += $credit;

            $processedLines[] = [
                'account_id'     => $accountId,
                'debit'          => $debit,
                'credit'         => $credit,
                'description'    => $line['description'] ?? $headerData['description'],
                'cost_center_id' => $line['cost_center_id'] ?? null,
            ];
        }

        // Floating point safe comparison (Difference must be less than 1 cent)
        if (abs($totalDebit - $totalCredit) >= 0.01) {
            throw new BusinessException(
                "Accounting Violation: The journal entry is unbalanced. Total Debits ({$totalDebit}) do not equal Total Credits ({$totalCredit}).",
                422
            );
        }

        // 2. Wrap insertion in a Database Transaction
        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedLines, $userId) {
            
            $entryToInsert = [
                'company_id'     => $companyId,
                'branch_id'      => $branchId,
                'entry_no'       => $this->journalRepo->generateEntryNumber($companyId),
                'entry_date'     => $headerData['entry_date'] ?? date('Y-m-d'),
                'reference_type' => $headerData['reference_type'] ?? null,
                'reference_id'   => $headerData['reference_id'] ?? null,
                'description'    => $headerData['description'],
                'currency_id'    => $headerData['currency_id'] ?? null,
                'exchange_rate'  => $headerData['exchange_rate'] ?? 1.000000,
                'status'         => 'posted', // Entries created automatically are posted immediately
                'posted_by'      => $userId,
                'posted_at'      => date('Y-m-d H:i:s'),
                'created_by'     => $userId,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $journalEntryId = $this->journalRepo->create($entryToInsert);

            $this->journalRepo->bulkInsertLines($journalEntryId, $processedLines);

            return $journalEntryId;
        });
    }

    /**
     * Automatically generate a Journal Entry for a posted Sales Invoice.
     * This automates the work of the accountant.
     *
     * @param int $invoiceId
     * @param string $invoiceNo
     * @param float $grandTotal
     * @param float $subtotal
     * @param float $taxTotal
     * @param array $accountingConfig Array containing the mapping accounts (AR, Sales, Tax)
     * @param int $userId
     * @return int The ID of the generated Journal Entry
     * @throws BusinessException|\Throwable
     */
    public function postSalesInvoice(
        int $invoiceId, 
        string $invoiceNo, 
        float $grandTotal, 
        float $subtotal, 
        float $taxTotal, 
        array $accountingConfig, 
        int $userId
    ): int {
        
        // Ensure mapping accounts are provided by the caller (fetched from Company Settings)
        $receivableAccountId = $accountingConfig['ar_account_id'] ?? null;
        $salesAccountId = $accountingConfig['sales_account_id'] ?? null;
        $taxAccountId = $accountingConfig['tax_account_id'] ?? null;

        if (!$receivableAccountId || !$salesAccountId) {
            throw new BusinessException("Accounting settings are missing. Please configure AR and Sales accounts.", 500);
        }

        $headerData = [
            'entry_date'     => date('Y-m-d'),
            'reference_type' => 'sales_invoice',
            'reference_id'   => $invoiceId,
            'description'    => "Sales Invoice #{$invoiceNo}",
        ];

        $linesData = [];

        // 1. Debit: Accounts Receivable (Customer owes us Grand Total)
        $linesData[] = [
            'account_id'  => $receivableAccountId,
            'debit'       => $grandTotal,
            'credit'      => 0.00,
            'description' => "AR for Invoice #{$invoiceNo}"
        ];

        // 2. Credit: Sales Revenue (Income earned before tax)
        $linesData[] = [
            'account_id'  => $salesAccountId,
            'debit'       => 0.00,
            'credit'      => $subtotal,
            'description' => "Sales Revenue for Invoice #{$invoiceNo}"
        ];

        // 3. Credit: Taxes Payable (If applicable)
        if ($taxTotal > 0) {
            if (!$taxAccountId) {
                throw new BusinessException("Tax total is greater than zero but no Tax Liability account is configured.", 500);
            }
            
            $linesData[] = [
                'account_id'  => $taxAccountId,
                'debit'       => 0.00,
                'credit'      => $taxTotal,
                'description' => "VAT for Invoice #{$invoiceNo}"
            ];
        }

        // The generalized method will ensure (Debit = Credit) and save it securely.
        return $this->createJournalEntry($headerData, $linesData, $userId);
    }
}