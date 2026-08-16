<?php
// Path: app/Modules/Purchasing/Returns/Application/DebitNoteService.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Returns\Application;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\Purchasing\Returns\Domain\DebitNoteRepositoryInterface;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Debit Note (Purchase Returns)
 * يقوم بتسجيل مرتجعات الموردين، وسحب البضاعة آلياً من المخزن.
 */
class DebitNoteService
{
    protected DebitNoteRepositoryInterface $debitNoteRepo;
    protected StockService $stockService;
    protected TransactionManager $transaction;
    protected TenantContext $tenant;

    public function __construct(
        DebitNoteRepositoryInterface $debitNoteRepo,
        StockService $stockService,
        TransactionManager $transaction,
        TenantContext $tenant
    ) {
        $this->debitNoteRepo = $debitNoteRepo;
        $this->stockService = $stockService;
        $this->transaction = $transaction;
        $this->tenant = $tenant;
    }

    public function createDebitNote(array $headerData, array $itemsData, int $userId): int
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
                'warehouse_id' => (int) $item['warehouse_id'],
                'quantity'     => $qty,
                'unit_price'   => $price,
                'tax_amount'   => $tax,
                'total'        => round($lineTotal, 2),
            ];
        }

        $grandTotal = round($subtotal + $taxTotal, 2);

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedItems, $subtotal, $taxTotal, $grandTotal, $userId) {
            
            $dnData = [
                'company_id'          => $companyId,
                'branch_id'           => $branchId,
                'debit_note_no'       => $this->debitNoteRepo->generateDebitNoteNumber($companyId),
                'purchase_invoice_id' => $headerData['purchase_invoice_id'] ?? null,
                'supplier_id'         => $headerData['supplier_id'],
                'note_date'           => $headerData['note_date'],
                'currency_id'         => $headerData['currency_id'],
                'subtotal'            => $subtotal,
                'tax_total'           => $taxTotal,
                'grand_total'         => $grandTotal,
                'reason'              => $headerData['reason'] ?? '',
                'status'              => 'posted', // نعتمد الإرجاع فوراً لتأثير المخزن
                'created_by'          => $userId,
                'created_at'          => date('Y-m-d H:i:s')
            ];

            $debitNoteId = $this->debitNoteRepo->create($dnData);
            $this->debitNoteRepo->bulkInsertItems($debitNoteId, $processedItems);

            // سحب البضاعة المرجعة من المخازن فوراً لمنع بيعها للعملاء
            foreach ($processedItems as $item) {
                $this->stockService->recordMovement(
                    $item['product_id'],
                    $item['warehouse_id'],
                    $item['quantity'],
                    StockMovementType::OUT, // خروج بضاعة (مرتجع مشتريات)
                    'purchase_return',
                    $debitNoteId,
                    $companyId,
                    $userId,
                    $item['unit_price'], // التكلفة التي سترد للمورد
                    "Purchase Return DN-{$dnData['debit_note_no']}"
                );
            }

            // هنا يتم إطلاق Event لعمل القيد المحاسبي العكسي (AP Decrease, Inventory Decrease)

            return $debitNoteId;
        });
    }
}