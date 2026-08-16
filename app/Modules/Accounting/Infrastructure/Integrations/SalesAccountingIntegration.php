<?php
// Path: app/Modules/Accounting/Infrastructure/Integrations/SalesAccountingIntegration.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Integrations;

use App\Modules\Accounting\Application\Services\JournalEntryService;
use App\Modules\Accounting\Application\DTOs\CreateJournalEntryDTO;
use App\Modules\Accounting\Application\DTOs\JournalEntryLineDTO;

/**
 * Enterprise ACL: Translates Sales Module Events into Accounting Journal Entries automatically.
 */
class SalesAccountingIntegration
{
    public function __construct(private JournalEntryService $journalService) {}

    public function postSalesInvoice(int $companyId, int $userId, array $invoiceData): int
    {
        /*
         * Standard Sales Entry:
         * DR Accounts Receivable (Gross)
         * CR Sales Revenue (Net)
         * CR VAT Payable (Tax)
         */
        $lines = [
            new JournalEntryLineDTO(
                accountId: $invoiceData['ar_account_id'],
                debit: $invoiceData['grand_total'],
                credit: 0.00,
                description: "A/R for Invoice " . $invoiceData['invoice_no']
            ),
            new JournalEntryLineDTO(
                accountId: $invoiceData['revenue_account_id'],
                debit: 0.00,
                credit: $invoiceData['subtotal'],
                description: "Revenue from Invoice " . $invoiceData['invoice_no']
            )
        ];

        if (($invoiceData['tax_amount'] ?? 0) > 0) {
            $lines[] = new JournalEntryLineDTO(
                accountId: $invoiceData['tax_account_id'],
                debit: 0.00,
                credit: $invoiceData['tax_amount'],
                description: "VAT Collected on Invoice " . $invoiceData['invoice_no']
            );
        }

        $dto = new CreateJournalEntryDTO(
            companyId: $companyId,
            userId: $userId,
            entryDate: $invoiceData['invoice_date'],
            description: "Automated Sales Posting: " . $invoiceData['invoice_no'],
            lines: $lines,
            referenceType: 'Sales Invoice',
            referenceId: $invoiceData['invoice_id']
        );

        // Auto-post the entry to GL
        return $this->journalService->createAndPostEntry($dto, true);
    }
}