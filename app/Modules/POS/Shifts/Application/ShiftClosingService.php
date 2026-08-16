<?php
// Path: app/Modules/POS/Shifts/Application/ShiftClosingService.php

declare(strict_types=1);

namespace App\Modules\POS\Shifts\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Finance\Services\AccountingService;
use App\Core\Settings\BranchSettings;

/**
 * Enterprise POS: Shift Closing Service
 * إغلاق الوردية النقدية (Z-Report). يقارن النقدية الموجودة بالصندوق بالمبيعات المسجلة،
 * وفي حال وجود عجز (Cash Shortage) أو زيادة (Cash Overage)، يسجل قيداً محاسبياً أوتوماتيكياً لضبط الصندوق.
 */
class ShiftClosingService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected AccountingService $accountingEngine;
    protected BranchSettings $settings;

    public function __construct(
        DatabaseManager $db,
        TransactionManager $transaction,
        AccountingService $accountingEngine,
        BranchSettings $settings
    ) {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->accountingEngine = $accountingEngine;
        $this->settings = $settings;
    }

    public function closeShift(int $shiftId, float $actualCash, int $companyId, int $userId): array
    {
        return $this->transaction->execute(function () use ($shiftId, $actualCash, $companyId, $userId) {
            
            // 1. جلب بيانات الوردية (مع إغلاق السجل لمنع العمليات المتزامنة)
            $shift = $this->db->connection()->selectOne("SELECT * FROM pos_shifts WHERE id = ? FOR UPDATE", [$shiftId]);

            if (!$shift || $shift['status'] === 'closed') {
                throw new BusinessException("Shift is invalid or already closed.");
            }

            if ((int) $shift['user_id'] !== $userId) {
                throw new BusinessException("You can only close your own shift.");
            }

            $openingAmount = (float) $shift['opening_amount'];

            // 2. جلب إجمالي المبيعات "النقدية" التي تمت في هذه الوردية
            $cashSales = $this->db->connection()->selectOne(
                "SELECT SUM(grand_total) as total_cash FROM pos_orders WHERE shift_id = ? AND payment_method = 'cash' AND status = 'completed'",
                [$shiftId]
            );

            $expectedCash = $openingAmount + (float) ($cashSales['total_cash'] ?? 0.0);
            $difference = round($actualCash - $expectedCash, 2);

            // 3. تحديث الوردية وإغلاقها
            $this->db->connection()->update(
                "UPDATE pos_shifts SET closed_at = ?, closing_amount = ?, expected_amount = ?, status = 'closed', updated_at = ? WHERE id = ?",
                [date('Y-m-d H:i:s'), $actualCash, $expectedCash, date('Y-m-d H:i:s'), $shiftId]
            );

            // 4. المعالجة المحاسبية التلقائية للفروقات (Shortage / Overage)
            if ($difference !== 0.0) {
                $this->postDifferenceJournalEntry($difference, $shiftId, $companyId, $userId);
            }

            return [
                'expected_cash' => $expectedCash,
                'actual_cash'   => $actualCash,
                'difference'    => $difference, // سالب = عجز، موجب = زيادة
            ];
        });
    }

    protected function postDifferenceJournalEntry(float $difference, int $shiftId, int $companyId, int $userId): void
    {
        // جلب حسابات التسوية من إعدادات الفرع/الشركة
        $posCashAccount = (int) $this->settings->get('pos.cash_account_id');
        $shortageAccount = (int) $this->settings->get('pos.shortage_expense_account_id');
        $overageAccount = (int) $this->settings->get('pos.overage_revenue_account_id');

        if (!$posCashAccount || !$shortageAccount || !$overageAccount) {
            throw new BusinessException("POS Reconciliation accounts are not fully configured in Branch Settings.");
        }

        $header = [
            'entry_date'     => date('Y-m-d'),
            'description'    => "POS Shift Closing Reconciliation - Shift #{$shiftId}",
            'reference_type' => 'pos_shift',
            'reference_id'   => $shiftId,
        ];

        $lines = [];

        if ($difference < 0) {
            // عجز (Shortage): مصروف العجز مدين، الصندوق دائن لتقليله للمبلغ الفعلي
            $absDiff = abs($difference);
            $lines[] = ['account_id' => $shortageAccount, 'debit' => $absDiff, 'credit' => 0.0];
            $lines[] = ['account_id' => $posCashAccount, 'debit' => 0.0, 'credit' => $absDiff];
        } else {
            // زيادة (Overage): الصندوق مدين، إيرادات الزيادة دائن
            $lines[] = ['account_id' => $posCashAccount, 'debit' => $difference, 'credit' => 0.0];
            $lines[] = ['account_id' => $overageAccount, 'debit' => 0.0, 'credit' => $difference];
        }

        $this->accountingEngine->createJournalEntry($header, $lines, $userId);
    }
}