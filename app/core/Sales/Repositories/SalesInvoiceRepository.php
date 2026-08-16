<?php
// Path: app/Core/Sales/Repositories/SalesInvoiceRepository.php

declare(strict_types=1);

namespace App\Core\Sales\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Sales Invoice Repository
 * Manages Invoice Headers.
 */
class SalesInvoiceRepository extends BaseRepository
{
    protected string $table = 'sales_invoices';
    protected bool $useTenantScope = true;

    /**
     * SalesInvoiceRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * Change the status of an invoice to 'posted'.
     * This makes the invoice immutable and ready for Journal Entry creation.
     *
     * @param int $invoiceId
     * @param int|null $journalEntryId
     * @return int Affected rows
     */
    public function markAsPosted(int $invoiceId, ?int $journalEntryId = null): int
    {
        $data = ['status' => 'posted'];
        
        if ($journalEntryId !== null) {
            $data['journal_entry_id'] = $journalEntryId;
        }

        return $this->update($invoiceId, $data);
    }

    /**
     * Generates a unique sequential invoice number for the current company.
     * Format: INV-YYYYMM-XXXX (e.g., INV-202608-0001)
     * Note: In a fully scaled environment, this would hit the `document_sequences` table with a row lock.
     *
     * @param int $companyId
     * @return string
     */
    public function generateInvoiceNumber(int $companyId): string
    {
        $prefix = 'INV-' . date('Ym') . '-';
        
        $lastInvoice = $this->newQuery()
            ->select(['invoice_no'])
            ->where('company_id', '=', $companyId)
            ->where('invoice_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastInvoice) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastInvoice['invoice_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }
}