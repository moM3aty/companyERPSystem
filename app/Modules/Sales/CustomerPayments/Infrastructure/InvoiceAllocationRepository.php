<?php
// Path: app/Modules/Sales/CustomerPayments/Infrastructure/InvoiceAllocationRepository.php

declare(strict_types=1);

namespace App\Modules\Sales\CustomerPayments\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\CustomerPayments\Domain\InvoiceAllocationRepositoryInterface;

class InvoiceAllocationRepository extends BaseRepository implements InvoiceAllocationRepositoryInterface
{
    protected string $table = 'sales_invoice_allocations';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function getUnallocatedReceiptBalance(int $receiptId, int $companyId): float
    {
        // 1. جلب المبلغ الأساسي لسند القبض
        $receipt = $this->db->connection()->selectOne(
            "SELECT amount FROM treasury_receipts WHERE id = ? AND company_id = ? AND status = 'posted'",
            [$receiptId, $companyId]
        );

        if (!$receipt) {
            return 0.0;
        }

        $totalReceiptAmount = (float) $receipt['amount'];

        // 2. جلب إجمالي ما تم تخصيصه مسبقاً
        $allocated = $this->db->connection()->selectOne(
            "SELECT SUM(allocated_amount) as total_allocated FROM {$this->table} WHERE receipt_id = ?",
            [$receiptId]
        );

        $totalAllocated = (float) ($allocated['total_allocated'] ?? 0.0);

        return round($totalReceiptAmount - $totalAllocated, 2);
    }
}