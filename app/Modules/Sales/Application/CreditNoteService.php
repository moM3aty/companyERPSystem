<?php
// Path: app/Modules/Sales/CreditNotes/Application/CreditNoteService.php

declare(strict_types=1);

namespace App\Modules\Sales\CreditNotes\Application;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\Sales\CreditNotes\Domain\CreditNoteRepositoryInterface;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Credit Note Engine
 * يقوم بحساب المرتجعات بدقة، ويفوض الـ StockService لإرجاع البضاعة للمخزن،
 * ويجهز البيانات للترحيل المحاسبي.
 */
class CreditNoteService
{
    protected CreditNoteRepositoryInterface $creditNoteRepo;
    protected StockService $stockService;
    protected TransactionManager $transaction;
    protected TenantContext $tenant;

    public function __construct(
        CreditNoteRepositoryInterface $creditNoteRepo,
        StockService $stockService,
        TransactionManager $transaction,
        TenantContext $tenant
    ) {
        $this->creditNoteRepo = $creditNoteRepo;
        $this->stockService = $stockService;
        $this->transaction = $transaction;
        $this->tenant = $tenant;
    }

    public function createCreditNote(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        $subtotal = 0.0;
        $taxTotal = 0.0;
        $processedItems = [];

        foreach ($itemsData as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $tax = (float) $item['tax_amount'];

            $lineTotal = ($qty * $price) + $tax;

            $subtotal += ($qty * $price);
            $taxTotal += $tax;

            $processedItems[] = [
                'product_id'   => (int) $item['product_id'],
                'warehouse_id' => $item['warehouse_id'] ?? null,
                'quantity'     => $qty,
                'unit_price'   => $price,
                'tax_amount'   => $tax,
                'total'        => round($lineTotal, 2),
            ];
        }

        $grandTotal = round($subtotal + $taxTotal, 2);

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedItems, $subtotal, $taxTotal, $grandTotal, $userId) {
            
            $cnData = [
                'company_id'     => $companyId,
                'branch_id'      => $branchId,
                'credit_note_no' => $this->creditNoteRepo->generateCreditNoteNumber($companyId),
                'invoice_id'     => $headerData['invoice_id'] ?? null,
                'customer_id'    => $headerData['customer_id'],
                'note_date'      => $headerData['note_date'],
                'currency_id'    => $headerData['currency_id'],
                'subtotal'       => $subtotal,
                'tax_total'      => $taxTotal,
                'grand_total'    => $grandTotal,
                'reason'         => $headerData['reason'] ?? '',
                'status'         => 'posted', // يتم الاعتماد للإرجاع فوراً
                'created_by'     => $userId,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $creditNoteId = $this->creditNoteRepo->create($cnData);
            $this->creditNoteRepo->bulkInsertItems($creditNoteId, $processedItems);

            // إرجاع البضاعة المادية إلى المخازن إن كانت قابلة للتخزين وتم تمرير المخزن
            foreach ($processedItems as $item) {
                if ($item['warehouse_id']) {
                    $this->stockService->recordMovement(
                        $item['product_id'],
                        (int) $item['warehouse_id'],
                        $item['quantity'],
                        StockMovementType::IN, // دخول بضاعة مرتجعة
                        'sales_return',
                        $creditNoteId,
                        $companyId,
                        $userId,
                        0.0, // التكلفة ستحسب حسب الـ Moving Average داخلياً
                        "Sales Return CN-{$cnData['credit_note_no']}"
                    );
                }
            }

            // ملاحظة: يتم هنا إطلاق Event ليقوم الـ Accounting Engine بتسجيل القيد المعاكس
            // EventBus::publish(new CreditNotePostedEvent(...));

            return $creditNoteId;
        });
    }
}