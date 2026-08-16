<?php
// Path: app/Modules/Treasury/Receipts/Application/ReceiptService.php

declare(strict_types=1);

namespace App\Modules\Treasury\Receipts\Application;

use App\Modules\Treasury\Receipts\Domain\ReceiptRepositoryInterface;
use App\Modules\Treasury\Accounts\Domain\TreasuryAccountRepositoryInterface;
use App\Modules\Treasury\Receipts\Domain\Events\ReceiptPostedEvent;
use App\Core\Finance\Services\AccountingService;
use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Receipt Creation
 * القلب النابض الذي يربط الخزينة بالمحاسبة. يقوم بإنشاء السند، ثم يطلب من Core المحاسبة توليد قيد اليومية.
 */
class ReceiptService
{
    protected ReceiptRepositoryInterface $receiptRepo;
    protected TreasuryAccountRepositoryInterface $accountRepo;
    protected AccountingService $accountingEngine;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;
    protected EventBus $eventBus;

    public function __construct(
        ReceiptRepositoryInterface $receiptRepo,
        TreasuryAccountRepositoryInterface $accountRepo,
        AccountingService $accountingEngine,
        TransactionManager $transaction,
        DatabaseManager $db,
        EventBus $eventBus
    ) {
        $this->receiptRepo = $receiptRepo;
        $this->accountRepo = $accountRepo;
        $this->accountingEngine = $accountingEngine;
        $this->transaction = $transaction;
        $this->db = $db;
        $this->eventBus = $eventBus;
    }

    /**
     * إنشاء سند قبض وترحيله محاسبياً في عملية واحدة معزولة (Atomic).
     *
     * @param array $data
     * @param int $companyId
     * @param int $userId
     * @return array
     * @throws BusinessException|\Throwable
     */
    public function createAndPostReceipt(array $data, int $companyId, int $userId): array
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            // 1. جلب حساب الخزينة للتأكد من حساب الـ GL المرتبط به
            $this->accountRepo->setTenantId($companyId);
            $treasuryAccount = $this->accountRepo->findOrFail((int) $data['treasury_account_id']);
            
            if (!$treasuryAccount['gl_account_id']) {
                throw new BusinessException("The selected treasury/bank account is not linked to the Chart of Accounts.", 500);
            }

            $debitGlAccountId = (int) $treasuryAccount['gl_account_id'];
            $creditGlAccountId = (int) $data['credit_account_id'];
            $amount = (float) $data['amount'];
            $description = $data['description'];

            // 2. تجهيز بيانات السند
            $receiptNo = $this->receiptRepo->generateReceiptNumber($companyId);
            
            $receiptData = array_merge($data, [
                'company_id' => $companyId,
                'receipt_no' => $receiptNo,
                'status'     => 'posted', // السندات المالية ترحل فوراً في أنظمة الـ ERP الدقيقة
                'created_by' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // 3. بناء هيكل القيد المحاسبي (Journal Entry) لإرساله لـ Accounting Engine
            $jeHeader = [
                'entry_date'     => $data['receipt_date'],
                'description'    => "Receipt #{$receiptNo} - {$description}",
                'reference_type' => 'receipt',
                'currency_id'    => $data['currency_id'] ?? null,
            ];

            $jeLines = [
                // المدين (الخزينة زادت)
                [
                    'account_id'  => $debitGlAccountId,
                    'debit'       => $amount,
                    'credit'      => 0.00,
                    'description' => $description
                ],
                // الدائن (العميل أو الإيراد)
                [
                    'account_id'  => $creditGlAccountId,
                    'debit'       => 0.00,
                    'credit'      => $amount,
                    'description' => $description
                ]
            ];

            // 4. استدعاء المحرك المحاسبي لتسجيل القيد (سيقوم المحرك بوزن القيد أمنياً)
            $journalEntryId = $this->accountingEngine->createJournalEntry($jeHeader, $jeLines, $userId);

            // 5. حفظ السند وربطه برقم القيد المولد
            $receiptData['journal_entry_id'] = $journalEntryId;
            $receiptId = $this->receiptRepo->create($receiptData);

            // 6. تحديث القيد المحاسبي لربطه برقم السند الفعلي (باستخدام الداتابيز المدمجة الآن)
            $this->db->connection()->update(
                "UPDATE journal_entries SET reference_id = ? WHERE id = ?",
                [$receiptId, $journalEntryId]
            );

            // 7. إطلاق الحدث لإبلاغ الأنظمة الأخرى (الـ Notifications أو الـ Audit)
            $this->eventBus->publish(new ReceiptPostedEvent($receiptId, $companyId, $amount));

            $this->receiptRepo->setTenantId($companyId);
            return $this->receiptRepo->findOrFail($receiptId);
        });
    }
}