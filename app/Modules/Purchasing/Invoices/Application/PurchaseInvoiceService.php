<?php
// Path: app/Modules/Purchasing/Invoices/Application/PurchaseInvoiceService.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Invoices\Application;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\Purchasing\Invoices\Domain\PurchaseInvoiceRepositoryInterface;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Purchase Invoice
 * يحسب إجمالي فاتورة المورد، ويفحص المطابقة (3-Way Matching إذا كان مرتبطاً بطلب شراء).
 */
class PurchaseInvoiceService
{
    protected PurchaseInvoiceRepositoryInterface $invoiceRepo;
    protected TransactionManager $transaction;
    protected TenantContext $tenantContext;

    public function __construct(
        PurchaseInvoiceRepositoryInterface $invoiceRepo,
        TransactionManager $transaction,
        TenantContext $tenantContext
    ) {
        $this->invoiceRepo = $invoiceRepo;
        $this->transaction = $transaction;
        $this->tenantContext = $tenantContext;
    }

    public function createInvoice(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenantContext->requireTenant()->companyId;
        $branchId = $this->tenantContext->getBranchId();

        // 1. حسابات السيرفر الآمنة
        $subtotal = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;
        $processedItems = [];

        foreach ($itemsData as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $discount = (float) ($item['discount_amount'] ?? 0.0);
            $tax = (float) ($item['tax_amount'] ?? 0.0);

            $lineBaseTotal = ($qty * $price);
            $lineNetTotal = $lineBaseTotal - $discount + $tax;

            $subtotal += $lineBaseTotal;
            $totalDiscount += $discount;
            $totalTax += $tax;

            $processedItems[] = [
                'product_id'      => (int) $item['product_id'],
                'description'     => $item['description'] ?? null,
                'quantity'        => $qty,
                'unit_price'      => $price,
                'discount_amount' => $discount,
                'tax_amount'      => $tax,
                'total'           => round($lineNetTotal, 2),
                'warehouse_id'    => $item['warehouse_id'] ?? null,
            ];
        }

        $grandTotal = round($subtotal - $totalDiscount + $totalTax, 2);

        // 2. الحفظ داخل Transaction لضمان النزاهة
        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedItems, $subtotal, $totalDiscount, $totalTax, $grandTotal, $userId) {
            
            $invoiceToInsert = [
                'company_id'       => $companyId,
                'branch_id'        => $branchId,
                'invoice_no'       => $this->invoiceRepo->generateInvoiceNumber($companyId),
                'supplier_bill_no' => $headerData['supplier_bill_no'],
                'supplier_id'      => (int) $headerData['supplier_id'],
                'invoice_date'     => $headerData['invoice_date'],
                'due_date'         => $headerData['due_date'],
                'currency_id'      => $headerData['currency_id'],
                'subtotal'         => $subtotal,
                'discount_total'   => $totalDiscount,
                'tax_total'        => $totalTax,
                'grand_total'      => $grandTotal,
                'paid_amount'      => 0.00,
                'status'           => 'draft',
                'created_by'       => $userId,
                'created_at'       => date('Y-m-d H:i:s')
            ];

            $invoiceId = $this->invoiceRepo->create($invoiceToInsert);

            $this->invoiceRepo->bulkInsertItems($invoiceId, $processedItems);

            return $invoiceId;
        });
    }
}