<?php
// Path: app/Modules/Treasury/Reconciliation/Application/ReconciliationService.php

declare(strict_types=1);

namespace App\Modules\Treasury\Reconciliation\Application;

use App\Modules\Treasury\Reconciliation\Domain\BankStatementRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Bank Reconciliation Engine
 * يقوم بمطابقة كشف حساب البنك المرفوع مع سندات القبض والصرف المسجلة في النظام
 * لضمان عدم وجود فروقات، أو لاكتشاف الرسوم البنكية.
 */
class ReconciliationService
{
    protected BankStatementRepositoryInterface $statementRepo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;

    public function __construct(
        BankStatementRepositoryInterface $statementRepo,
        TransactionManager $transaction,
        DatabaseManager $db
    ) {
        $this->statementRepo = $statementRepo;
        $this->transaction = $transaction;
        $this->db = $db;
    }

    /**
     * تشغيل خوارزمية المطابقة الآلية (Auto-Match).
     *
     * @param int $statementId
     * @param int $companyId
     * @return array إحصائيات المطابقة
     * @throws BusinessException|\Throwable
     */
    public function autoMatch(int $statementId, int $companyId): array
    {
        return $this->transaction->execute(function () use ($statementId, $companyId) {
            
            $this->statementRepo->setTenantId($companyId);
            $statement = $this->statementRepo->findOrFail($statementId);

            if ($statement['status'] === 'reconciled') {
                throw new BusinessException("This bank statement is already reconciled.");
            }

            $unmatchedLines = $this->statementRepo->getUnmatchedLines($statementId);
            $treasuryAccountId = (int) $statement['treasury_account_id'];
            $matchedCount = 0;

            foreach ($unmatchedLines as $line) {
                $amount = (float) $line['amount'];
                $date = $line['transaction_date'];
                $lineId = (int) $line['id'];

                if ($amount > 0) {
                    // إيداع -> نبحث عن سند قبض (Receipt) مطابق في المبلغ والتاريخ
                    $match = $this->db->connection()->selectOne(
                        "SELECT id FROM treasury_receipts 
                         WHERE treasury_account_id = ? AND amount = ? AND receipt_date = ? AND status = 'posted' 
                         LIMIT 1",
                        [$treasuryAccountId, $amount, $date]
                    );

                    if ($match) {
                        $this->linkMatch($lineId, 'receipt', (int) $match['id']);
                        $matchedCount++;
                    }
                } else {
                    // سحب -> نبحث عن سند صرف (Payment Voucher) مطابق في المبلغ (بالقيمة المطلقة)
                    $absAmount = abs($amount);
                    $match = $this->db->connection()->selectOne(
                        "SELECT id FROM treasury_payment_vouchers 
                         WHERE treasury_account_id = ? AND amount = ? AND voucher_date = ? AND status = 'posted' 
                         LIMIT 1",
                        [$treasuryAccountId, $absAmount, $date]
                    );

                    if ($match) {
                        $this->linkMatch($lineId, 'payment_voucher', (int) $match['id']);
                        $matchedCount++;
                    }
                }
            }

            // فحص ما إذا تم مطابقة جميع السطور
            $remaining = count($unmatchedLines) - $matchedCount;
            if ($remaining === 0 && count($unmatchedLines) > 0) {
                $this->statementRepo->update($statementId, ['status' => 'reconciled', 'updated_at' => date('Y-m-d H:i:s')]);
            }

            return [
                'total_lines_checked' => count($unmatchedLines),
                'auto_matched_lines'  => $matchedCount,
                'remaining_unmatched' => $remaining,
            ];
        });
    }

    protected function linkMatch(int $lineId, string $docType, int $docId): void
    {
        $this->db->connection()->update(
            "UPDATE treasury_bank_statement_lines 
             SET is_matched = 1, matched_document_type = ?, matched_document_id = ? 
             WHERE id = ?",
            [$docType, $docId, $lineId]
        );
    }
}