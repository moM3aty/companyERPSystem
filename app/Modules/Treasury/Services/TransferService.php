<?php
// Path: app/Modules/Treasury/Services/TransferService.php

declare(strict_types=1);

namespace App\Modules\Treasury\Services;

use App\Modules\Treasury\Repositories\TransferRepository;
use App\Modules\Treasury\Events\FundsTransferred;
use App\Modules\Treasury\Exceptions\InsufficientFundsException;
use App\Core\Finance\Services\AccountingService;
use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Treasury Transfer Engine
 * يدير نقل الأموال بين الصناديق/البنوك ويسجل القيد المحاسبي.
 */
class TransferService
{
    protected TransferRepository $transferRepo;
    protected AccountingService $accountingEngine;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;
    protected EventBus $eventBus;

    public function __construct(
        TransferRepository $transferRepo,
        AccountingService $accountingEngine,
        TransactionManager $transaction,
        DatabaseManager $db,
        EventBus $eventBus
    ) {
        $this->transferRepo = $transferRepo;
        $this->accountingEngine = $accountingEngine;
        $this->transaction = $transaction;
        $this->db = $db;
        $this->eventBus = $eventBus;
    }

    public function executeTransfer(array $data, int $companyId, int $userId): array
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            // 1. جلب الحسابات للتأكد من حسابات הـ GL المرتبطة
            $fromAccount = $this->db->connection()->selectOne("SELECT * FROM treasury_accounts WHERE id = ? FOR UPDATE", [$data['from_account_id']]);
            $toAccount = $this->db->connection()->selectOne("SELECT * FROM treasury_accounts WHERE id = ? FOR UPDATE", [$data['to_account_id']]);

            if (!$fromAccount['gl_account_id'] || !$toAccount['gl_account_id']) {
                throw new BusinessException("Both treasury accounts must be linked to a GL Account.", 500);
            }

            // 2. التحقق من الرصيد المتوفر في حساب المصدر (Pessimistic Check on GL)
            // (في بيئة العمل، نجلب رصيد حساب ה-GL من الميزانية، أو من جدول الأرصدة اللحظية)
            $balanceRow = $this->db->connection()->selectOne(
                "SELECT SUM(debit - credit) as balance FROM journal_entry_lines jl JOIN journal_entries je ON jl.journal_entry_id = je.id WHERE jl.account_id = ? AND je.status = 'posted'",
                [$fromAccount['gl_account_id']]
            );
            
            $availableBalance = (float) ($balanceRow['balance'] ?? 0.0);
            $amount = (float) $data['amount'];

            // في الشركات الكبيرة، البنوك قد تسمح بالسحب على المكشوف (Overdraft)، لكن الخزينة لا تسمح
            if ($fromAccount['type'] === 'cash' && $availableBalance < $amount) {
                throw new InsufficientFundsException($fromAccount['name'], $amount, $availableBalance);
            }

            // 3. تجهيز بيانات التحويل
            $transferNo = $this->transferRepo->generateTransferNumber($companyId);
            
            $transferData = [
                'company_id'      => $companyId,
                'transfer_no'     => $transferNo,
                'from_account_id' => $data['from_account_id'],
                'to_account_id'   => $data['to_account_id'],
                'amount'          => $amount,
                'transfer_date'   => $data['transfer_date'],
                'description'     => $data['description'],
                'status'          => 'completed', // نعتبره اكتمل فوراً 
                'created_by'      => $userId,
                'created_at'      => date('Y-m-d H:i:s')
            ];

            // 4. القيد المحاسبي (الدائن: المصدر، المدين: الهدف)
            $jeHeader = [
                'entry_date'     => $data['transfer_date'],
                'description'    => "Fund Transfer #{$transferNo} - {$data['description']}",
                'reference_type' => 'internal_transfer',
            ];

            $jeLines = [
                // المدين (المستقبل زاد)
                ['account_id' => $toAccount['gl_account_id'], 'debit' => $amount, 'credit' => 0.00],
                // الدائن (المصدر نقص)
                ['account_id' => $fromAccount['gl_account_id'], 'debit' => 0.00, 'credit' => $amount]
            ];

            $journalEntryId = $this->accountingEngine->createJournalEntry($jeHeader, $jeLines, $userId);

            // 5. حفظ التحويل
            $transferData['journal_entry_id'] = $journalEntryId;
            $transferId = $this->transferRepo->create($transferData);

            $this->eventBus->publish(new FundsTransferred($transferId, $companyId, $amount, $transferNo));

            $this->transferRepo->setTenantId($companyId);
            return $this->transferRepo->findOrFail($transferId);
        });
    }
}