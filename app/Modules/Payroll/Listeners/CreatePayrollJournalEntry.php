<?php
// Path: app/Modules/Payroll/Listeners/CreatePayrollJournalEntry.php

declare(strict_types=1);

namespace App\Modules\Payroll\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Database\DatabaseManager;
use App\Core\Finance\Services\AccountingService;
use App\Core\Settings\CompanySettings;
use App\Core\Exceptions\BusinessException;
use App\Modules\Payroll\PayrollRuns\Domain\Events\PayrollRunProcessedEvent;

/**
 * Enterprise Listener: Create Payroll Journal Entry
 * الجوهرة المحاسبية! يستمع لحدث اعتماد الرواتب ويقوم بتوليد القيد المزدوج تلقائياً.
 * (مدين: مصروف الرواتب | دائن: الرواتب المستحقة + الضرائب/الخصومات).
 */
class CreatePayrollJournalEntry implements EventListener
{
    protected DatabaseManager $db;
    protected AccountingService $accounting;
    protected CompanySettings $settings;

    public function __construct(DatabaseManager $db, AccountingService $accounting, CompanySettings $settings)
    {
        $this->db = $db;
        $this->accounting = $accounting;
        $this->settings = $settings;
    }

    public function handle(Event $event): void
    {
        if (!$event instanceof PayrollRunProcessedEvent) {
            return;
        }

        $runId = (int) $event->entityId;
        $companyId = $event->companyId;

        // 1. جلب بيانات المسير
        $run = $this->db->connection()->selectOne("SELECT * FROM payroll_runs WHERE id = ?", [$runId]);
        if (!$run) return;

        // 2. جلب الحسابات المربوطة من إعدادات الشركة
        $expenseAccountId = (int) $this->settings->get('accounting.salary_expense_account_id');
        $payableAccountId = (int) $this->settings->get('accounting.salary_payable_account_id');
        $taxPayableAccountId = (int) $this->settings->get('accounting.payroll_tax_payable_id');

        if (!$expenseAccountId || !$payableAccountId) {
            throw new BusinessException("Cannot post payroll journal entry. Payroll accounts are not configured in settings.");
        }

        $totalBasic = (float) $run['total_basic'];
        $totalAllowances = (float) $run['total_allowances'];
        $totalDeductions = (float) $run['total_deductions'];
        $netTotal = (float) $run['net_total'];

        $totalExpense = $totalBasic + $totalAllowances;

        // 3. بناء القيد
        $header = [
            'entry_date'     => date('Y-m-t', strtotime($run['run_period'] . '-01')), // آخر يوم في الشهر
            'description'    => "Payroll Expense for period: {$run['run_period']}",
            'reference_type' => 'payroll_run',
            'reference_id'   => $runId,
        ];

        $lines = [];

        // الطرف المدين (مصروف الرواتب)
        $lines[] = ['account_id' => $expenseAccountId, 'debit' => $totalExpense, 'credit' => 0.0];

        // الطرف الدائن (الخصومات والضرائب إن وجدت)
        if ($totalDeductions > 0) {
            if (!$taxPayableAccountId) {
                throw new BusinessException("Deductions exist but Tax Payable account is not configured.");
            }
            $lines[] = ['account_id' => $taxPayableAccountId, 'debit' => 0.0, 'credit' => $totalDeductions];
        }

        // الطرف الدائن (الصافي المستحق للموظفين - حساب وسيط حتى يتم الدفع من الخزينة)
        $lines[] = ['account_id' => $payableAccountId, 'debit' => 0.0, 'credit' => $netTotal];

        // 4. ترحيل القيد
        $journalEntryId = $this->accounting->createJournalEntry($header, $lines, 0); // 0 = System Auto

        // ربط القيد بالمسير
        $this->db->connection()->update(
            "UPDATE payroll_runs SET journal_entry_id = ?, status = 'posted' WHERE id = ?",
            [$journalEntryId, $runId]
        );
    }
}