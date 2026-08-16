<?php
// Path: app/Modules/Accounting/Infrastructure/Integrations/PurchasingAccountingIntegration.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Integrations;

use App\Modules\Accounting\Application\Services\JournalEntryService;
use App\Modules\Accounting\Application\DTOs\CreateJournalEntryDTO;
use App\Modules\Accounting\Application\DTOs\JournalEntryLineDTO;

class PurchasingAccountingIntegration
{
    public function __construct(private JournalEntryService $journalService) {}

    public function postVendorBill(int $companyId, int $userId, array $billData): int
    {
        /*
         * Standard Purchase Entry:
         * DR Inventory / Expense (Net)
         * DR VAT Receivable (Tax)
         * CR Accounts Payable (Gross)
         */
        $lines = [
            new JournalEntryLineDTO(
                accountId: $billData['expense_account_id'],
                debit: $billData['subtotal'],
                credit: 0.00,
                description: "Expense/Inventory for Bill " . $billData['bill_no']
            )
        ];

        if (($billData['tax_amount'] ?? 0) > 0) {
            $lines[] = new JournalEntryLineDTO(
                accountId: $billData['tax_account_id'],
                debit: $billData['tax_amount'],
                credit: 0.00,
                description: "VAT Input on Bill " . $billData['bill_no']
            );
        }

        $lines[] = new JournalEntryLineDTO(
            accountId: $billData['ap_account_id'],
            debit: 0.00,
            credit: $billData['grand_total'],
            description: "A/P for Bill " . $billData['bill_no']
        );

        $dto = new CreateJournalEntryDTO(
            companyId: $companyId,
            userId: $userId,
            entryDate: $billData['bill_date'],
            description: "Automated AP Posting: " . $billData['bill_no'],
            lines: $lines,
            referenceType: 'Vendor Bill',
            referenceId: $billData['bill_id']
        );

        return $this->journalService->createAndPostEntry($dto, true);
    }
}