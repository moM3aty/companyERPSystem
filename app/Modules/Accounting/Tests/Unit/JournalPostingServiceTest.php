<?php
// Path: app/Modules/Accounting/Tests/Unit/JournalPostingServiceTest.php

declare(strict_types=1);

namespace App\Modules\Accounting\Tests\Unit;

use App\Modules\Accounting\Core\JournalPostingService;
use App\Modules\Accounting\Core\FinancialCalculationService;
use App\Modules\Accounting\Core\AccountingPeriodService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Enterprise Unit Test: Journal Posting Rules
 * نختبر هنا أن النظام سيمنع تماماً أي قيد غير متزن من الترحيل.
 */
class JournalPostingServiceTest extends TestCase
{
    private JournalPostingService $postingService;

    protected function setUp(): void
    {
        // Mocking the DB-dependent Period Service to always return true for testing
        $periodServiceMock = $this->createMock(AccountingPeriodService::class);
        $periodServiceMock->method('validateDateIsOpen')->willReturn(true);

        $calcService = new FinancialCalculationService();

        $this->postingService = new JournalPostingService($periodServiceMock, $calcService);
    }

    public function test_it_throws_exception_on_unbalanced_entry(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Unbalanced Entry");

        $unbalancedLines = [
            ['account_id' => 1, 'debit' => 1000.00, 'credit' => 0.00],
            ['account_id' => 2, 'debit' => 0.00, 'credit' => 900.00] // Missing 100
        ];

        $this->postingService->validateEntry(1, '2026-08-15', $unbalancedLines);
    }

    public function test_it_accepts_perfectly_balanced_entry(): void
    {
        $balancedLines = [
            ['account_id' => 1, 'debit' => 1000.00, 'credit' => 0.00],
            ['account_id' => 2, 'debit' => 0.00, 'credit' => 1000.00]
        ];

        // Should not throw any exception
        $this->postingService->validateEntry(1, '2026-08-15', $balancedLines);
        $this->assertTrue(true); // Assertion passed
    }
}