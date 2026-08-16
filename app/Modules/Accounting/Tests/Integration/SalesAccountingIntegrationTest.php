<?php
// Path: app/Modules/Accounting/Tests/Integration/SalesAccountingIntegrationTest.php

declare(strict_types=1);

namespace App\Modules\Accounting\Tests\Integration;

use App\Modules\Accounting\Infrastructure\Integrations\SalesAccountingIntegration;
use App\Modules\Accounting\Application\Services\JournalEntryService;
use App\Modules\Accounting\Application\DTOs\CreateJournalEntryDTO;
use PHPUnit\Framework\TestCase;

/**
 * Enterprise Integration Test: Sales to Accounting
 * نختبر هنا الترابط الفعلي؛ كيف يقوم موديول المبيعات بترجمة الفاتورة إلى قيد محاسبي صحيح.
 */
class SalesAccountingIntegrationTest extends TestCase
{
    private SalesAccountingIntegration $integration;
    private $journalServiceMock;

    protected function setUp(): void
    {
        // We mock the actual JournalService to intercept the DTO it receives
        $this->journalServiceMock = $this->createMock(JournalEntryService::class);
        
        // Instantiate the Integration class with the mocked service
        $this->integration = new SalesAccountingIntegration($this->journalServiceMock);
    }

    public function test_sales_invoice_posts_correct_gl_entry(): void
    {
        $companyId = 1;
        $userId = 1;
        
        // 1. Mock Data from a Sales Invoice
        $invoiceData = [
            'invoice_id' => 89,
            'invoice_no' => 'INV-2026-089',
            'invoice_date' => '2026-08-16',
            'ar_account_id' => 1200,      // Accounts Receivable
            'revenue_account_id' => 4000, // Sales Revenue
            'tax_account_id' => 2200,     // VAT Payable
            'subtotal' => 10000.00,
            'tax_amount' => 1500.00,
            'grand_total' => 11500.00,
        ];

        // 2. Expectation: We expect the JournalEntryService to receive a specific DTO
        $this->journalServiceMock->expects($this->once())
            ->method('createAndPostEntry')
            ->with($this->callback(function (CreateJournalEntryDTO $dto) use ($invoiceData) {
                
                // Assert the Header translated correctly
                $this->assertEquals($invoiceData['invoice_date'], $dto->entryDate);
                $this->assertEquals('Sales Invoice', $dto->referenceType);
                $this->assertEquals($invoiceData['invoice_id'], $dto->referenceId);
                
                // Assert Lines translated correctly (3 lines: AR, Rev, Tax)
                $lines = $dto->lines;
                $this->assertCount(3, $lines);

                // Line 1: Debit A/R
                $this->assertEquals($invoiceData['ar_account_id'], $lines[0]->accountId);
                $this->assertEquals(11500.00, $lines[0]->debit);

                // Line 2: Credit Revenue
                $this->assertEquals($invoiceData['revenue_account_id'], $lines[1]->accountId);
                $this->assertEquals(10000.00, $lines[1]->credit);

                // Line 3: Credit VAT
                $this->assertEquals($invoiceData['tax_account_id'], $lines[2]->accountId);
                $this->assertEquals(1500.00, $lines[2]->credit);

                return true;
            }), $this->equalTo(true)) // Ensure autoPost is true
            ->willReturn(55); // Mock return Journal ID

        // 3. Trigger the Integration
        $resultId = $this->integration->postSalesInvoice($companyId, $userId, $invoiceData);

        // 4. Assert response
        $this->assertEquals(55, $resultId);
    }
}