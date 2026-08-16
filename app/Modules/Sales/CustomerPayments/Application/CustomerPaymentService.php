<?php
// Path: app/Modules/Sales/CustomerPayments/Application/CustomerPaymentService.php

declare(strict_types=1);

namespace App\Modules\Sales\CustomerPayments\Application;

use App\Modules\Sales\CustomerPayments\Domain\InvoiceAllocationRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Customer Payments (AR Settlement)
 * يعالج عملية تسديد فواتير المبيعات من سندات القبض بدقة ويمنع السداد المزدوج أو التجاوز.
 */
class CustomerPaymentService
{
    protected InvoiceAllocationRepositoryInterface $allocationRepo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;
    protected TenantContext $tenant;

    public function __construct(
        InvoiceAllocationRepositoryInterface $allocationRepo,
        TransactionManager $transaction,
        DatabaseManager $db,
        TenantContext $tenant
    ) {
        $this->allocationRepo = $allocationRepo;
        $this->transaction = $transaction;
        $this->db = $db;
        $this->tenant = $tenant;
    }

    /**
     * تخصيص سند قبض لتسديد فاتورة أو مجموعة فواتير.
     *
     * @param int $receiptId
     * @param array $allocations [['sales_invoice_id' => 1, 'amount' => 500]]
     * @param int $userId
     * @return void
     * @throws BusinessException|\Throwable
     */
    public function allocateReceipt(int $receiptId, array $allocations, int $userId): void
    {
        $companyId = $this->tenant->requireTenant()->companyId;

        $this->transaction->execute(function () use ($receiptId, $allocations, $companyId, $userId) {
            
            // 1. التحقق من الرصيد المتبقي في السند
            $unallocatedBalance = $this->allocationRepo->getUnallocatedReceiptBalance($receiptId, $companyId);
            
            $totalRequestedAllocation = array_sum(array_column($allocations, 'amount'));

            if ($totalRequestedAllocation > $unallocatedBalance) {
                throw new BusinessException("Cannot allocate {$totalRequestedAllocation}. The receipt only has {$unallocatedBalance} unallocated balance left.", 422);
            }

            foreach ($allocations as $allocation) {
                $invoiceId = (int) $allocation['sales_invoice_id'];
                $amountToAllocate = (float) $allocation['amount'];

                // 2. استخدام Row Lock لمنع السداد المزدوج في نفس اللحظة (Pessimistic Locking)
                $invoice = $this->db->connection()->selectOne(
                    "SELECT grand_total, paid_amount, status FROM sales_invoices WHERE id = ? AND company_id = ? FOR UPDATE",
                    [$invoiceId, $companyId]
                );

                if (!$invoice || $invoice['status'] === 'paid' || $invoice['status'] === 'voided') {
                    throw new BusinessException("Invoice [ID: {$invoiceId}] is either invalid, voided, or already fully paid.");
                }

                $grandTotal = (float) $invoice['grand_total'];
                $currentPaid = (float) $invoice['paid_amount'];
                $remainingDue = round($grandTotal - $currentPaid, 2);

                if ($amountToAllocate > $remainingDue) {
                    throw new BusinessException("Cannot allocate {$amountToAllocate} to Invoice [ID: {$invoiceId}]. The remaining due is only {$remainingDue}.");
                }

                // 3. إنشاء سجل التخصيص
                $this->allocationRepo->create([
                    'company_id'       => $companyId,
                    'receipt_id'       => $receiptId,
                    'sales_invoice_id' => $invoiceId,
                    'allocated_amount' => $amountToAllocate,
                    'allocated_by'     => $userId,
                    'created_at'       => date('Y-m-d H:i:s'),
                ]);

                // 4. تحديث الفاتورة (إضافة المبلغ وإغلاق الفاتورة إن سُددت بالكامل)
                $newPaidAmount = $currentPaid + $amountToAllocate;
                $newStatus = ($newPaidAmount >= $grandTotal) ? 'paid' : $invoice['status'];

                $this->db->connection()->update(
                    "UPDATE sales_invoices SET paid_amount = ?, status = ?, updated_at = ? WHERE id = ?",
                    [$newPaidAmount, $newStatus, date('Y-m-d H:i:s'), $invoiceId]
                );
            }
        });
    }
}