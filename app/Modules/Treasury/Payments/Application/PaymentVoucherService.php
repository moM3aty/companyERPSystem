<?php
// Path: app/Modules/Treasury/Payments/Application/PaymentVoucherService.php

declare(strict_types=1);

namespace App\Modules\Treasury\Payments\Application;

use App\Modules\Treasury\Payments\Domain\PaymentVoucherRepositoryInterface;
use App\Modules\Treasury\Accounts\Domain\TreasuryAccountRepositoryInterface;
use App\Modules\Treasury\Payments\Domain\Events\PaymentVoucherPostedEvent;
use App\Core\Finance\Services\AccountingService;
use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Payment Voucher
 * يدير عملية دفع الأموال وإنشاء قيد اليومية العكسي لسندات القبض (هنا الخزينة تصبح دائن والمصروف/المورد مدين).
 */
class PaymentVoucherService
{
    protected PaymentVoucherRepositoryInterface $voucherRepo;
    protected TreasuryAccountRepositoryInterface $accountRepo;
    protected AccountingService $accountingEngine;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;
    protected EventBus $eventBus;

    public function __construct(
        PaymentVoucherRepositoryInterface $voucherRepo,
        TreasuryAccountRepositoryInterface $accountRepo,
        AccountingService $accountingEngine,
        TransactionManager $transaction,
        DatabaseManager $db,
        EventBus $eventBus
    ) {
        $this->voucherRepo = $voucherRepo;
        $this->accountRepo = $accountRepo;
        $this->accountingEngine = $accountingEngine;
        $this->transaction = $transaction;
        $this->db = $db;
        $this->eventBus = $eventBus;
    }

    /**
     * إنشاء سند صرف وترحيله محاسبياً بشكل آمن (Atomic).
     *
     * @param array $data
     * @param int $companyId
     * @param int $userId
     * @return array
     * @throws BusinessException|\Throwable
     */
    public function createAndPostVoucher(array $data, int $companyId, int $userId): array
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            // 1. جلب حساب الخزينة للتأكد من حساب الـ GL المرتبط به
            $this->accountRepo->setTenantId($companyId);
            $treasuryAccount = $this->accountRepo->findOrFail((int) $data['treasury_account_id']);
            
            if (!$treasuryAccount['gl_account_id']) {
                throw new BusinessException("The selected treasury/bank account is not linked to the Chart of Accounts.", 500);
            }

            // في سند الصرف: الخزينة (أصل) تقل فتصبح دائناً، والمصروف/المورد يزيد فيصبح مديناً
            $creditGlAccountId = (int) $treasuryAccount['gl_account_id'];
            $debitGlAccountId = (int) $data['debit_account_id'];
            $amount = (float) $data['amount'];
            $description = $data['description'];

            // 2. تجهيز بيانات السند
            $voucherNo = $this->voucherRepo->generateVoucherNumber($companyId);
            
            $voucherData = array_merge($data, [
                'company_id' => $companyId,
                'voucher_no' => $voucherNo,
                'status'     => 'posted', 
                'created_by' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // 3. بناء هيكل القيد المحاسبي
            $jeHeader = [
                'entry_date'     => $data['voucher_date'],
                'description'    => "Payment Voucher #{$voucherNo} - {$description}",
                'reference_type' => 'payment_voucher',
                'currency_id'    => $data['currency_id'] ?? null,
            ];

            $jeLines = [
                // المدين (المصروف أو المورد زاد)
                [
                    'account_id'  => $debitGlAccountId,
                    'debit'       => $amount,
                    'credit'      => 0.00,
                    'description' => $description
                ],
                // الدائن (الخزينة/البنك قل)
                [
                    'account_id'  => $creditGlAccountId,
                    'debit'       => 0.00,
                    'credit'      => $amount,
                    'description' => $description
                ]
            ];

            // 4. استدعاء المحرك المحاسبي لتسجيل القيد
            $journalEntryId = $this->accountingEngine->createJournalEntry($jeHeader, $jeLines, $userId);

            // 5. حفظ السند
            $voucherData['journal_entry_id'] = $journalEntryId;
            $voucherId = $this->voucherRepo->create($voucherData);

            // 6. ربط القيد المحاسبي برقم السند
            $this->db->connection()->update(
                "UPDATE journal_entries SET reference_id = ? WHERE id = ?",
                [$voucherId, $journalEntryId]
            );

            // 7. إطلاق الحدث
            $this->eventBus->publish(new PaymentVoucherPostedEvent($voucherId, $companyId, $amount));

            $this->voucherRepo->setTenantId($companyId);
            return $this->voucherRepo->findOrFail($voucherId);
        });
    }
}