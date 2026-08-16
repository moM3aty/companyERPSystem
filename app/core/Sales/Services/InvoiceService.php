<?php
// Path: app/Core/Sales/Services/InvoiceService.php

declare(strict_types=1);

namespace App\Core\Sales\Services;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Sales\Repositories\SalesInvoiceRepository;
use App\Core\Sales\Repositories\SalesInvoiceItemRepository;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Invoice Service
 * Encapsulates the complex business logic and mathematical calculations for creating invoices.
 * NEVER trusts the frontend for totals. Recalculates everything securely on the server.
 */
class InvoiceService
{
    protected SalesInvoiceRepository $invoiceRepo;
    protected SalesInvoiceItemRepository $itemRepo;
    protected TransactionManager $transaction;
    protected TenantContext $tenantContext;

    /**
     * InvoiceService constructor.
     *
     * @param SalesInvoiceRepository $invoiceRepo
     * @param SalesInvoiceItemRepository $itemRepo
     * @param TransactionManager $transaction
     * @param TenantContext $tenantContext
     */
    public function __construct(
        SalesInvoiceRepository $invoiceRepo,
        SalesInvoiceItemRepository $itemRepo,
        TransactionManager $transaction,
        TenantContext $tenantContext
    ) {
        $this->invoiceRepo = $invoiceRepo;
        $this->itemRepo = $itemRepo;
        $this->transaction = $transaction;
        $this->tenantContext = $tenantContext;
    }

    /**
     * Create a completely new Sales Invoice.
     *
     * @param array $headerData Basic invoice info (customer_id, invoice_date, etc.)
     * @param array $itemsData Array of items with qty, unit_price, discount_amount, tax_amount
     * @param int $userId ID of the user creating the invoice
     * @return int The newly created Invoice ID
     * @throws BusinessException|\Throwable
     */
    public function createInvoice(array $headerData, array $itemsData, int $userId): int
    {
        if (empty($itemsData)) {
            throw new BusinessException("An invoice must contain at least one item.", 422);
        }

        $companyId = $this->tenantContext->requireTenant()->companyId;
        $branchId = $this->tenantContext->getBranchId();

        // 1. Enforce Server-Side Mathematical Accuracy (Do not trust Frontend)
        $subtotal = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;

        $processedItems = [];

        foreach ($itemsData as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $discount = (float) ($item['discount_amount'] ?? 0.0);
            $tax = (float) ($item['tax_amount'] ?? 0.0);

            // Calculate item line total: (Qty * Price) - Discount + Tax
            $lineBaseTotal = ($qty * $price);
            $lineNetTotal = $lineBaseTotal - $discount + $tax;

            // Accumulate invoice totals
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

        // 2. Wrap the insertion in a Database Transaction to guarantee ACID compliance
        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedItems, $subtotal, $totalDiscount, $totalTax, $grandTotal, $userId) {
            
            // Prepare Header Data
            $invoiceToInsert = [
                'company_id'       => $companyId,
                'branch_id'        => $branchId,
                'invoice_no'       => $this->invoiceRepo->generateInvoiceNumber($companyId),
                'customer_id'      => (int) $headerData['customer_id'],
                'invoice_date'     => $headerData['invoice_date'] ?? date('Y-m-d'),
                'due_date'         => $headerData['due_date'] ?? date('Y-m-d'),
                'currency_id'      => $headerData['currency_id'] ?? null,
                'subtotal'         => $subtotal,
                'discount_total'   => $totalDiscount,
                'tax_total'        => $totalTax,
                'grand_total'      => $grandTotal,
                'paid_amount'      => 0.00, // Always 0 on creation, handled by Payments Module later
                'status'           => 'draft',
                'created_by'       => $userId,
                'created_at'       => date('Y-m-d H:i:s')
            ];

            // Save Header
            $invoiceId = $this->invoiceRepo->create($invoiceToInsert);

            // Save Lines (Bulk Insert for maximum performance)
            $this->itemRepo->bulkInsert($invoiceId, $processedItems);

            return $invoiceId;
        });
    }
}