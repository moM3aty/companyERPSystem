<?php
// Path: app/Modules/Purchasing/Services/PurchaseQuotationService.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class PurchaseQuotationService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    public function awardQuotation(int $quotationId, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($quotationId, $companyId, $userId) {
            $quote = $this->db->connection()->selectOne(
                "SELECT * FROM purchase_quotations WHERE id = ? AND company_id = ? AND status = 'submitted' FOR UPDATE",
                [$quotationId, $companyId]
            );

            if (!$quote) {
                throw new BusinessException("Quotation not found or not in a valid state to be awarded.");
            }

            // Reject all other quotations for the same RFQ
            $this->db->connection()->update(
                "UPDATE purchase_quotations SET status = 'rejected', updated_at = ? WHERE rfq_id = ? AND id != ?",
                [date('Y-m-d H:i:s'), $quote['rfq_id'], $quotationId]
            );

            // Award this quotation
            $this->db->connection()->update(
                "UPDATE purchase_quotations SET status = 'awarded', updated_at = ? WHERE id = ?",
                [date('Y-m-d H:i:s'), $quotationId]
            );

            // Generate Purchase Order automatically
            $this->db->connection()->insert(
                "INSERT INTO purchase_orders (company_id, supplier_id, order_date, total_amount, status, created_by, created_at) 
                VALUES (?, ?, ?, ?, 'draft', ?, ?)",
                [$companyId, $quote['supplier_id'], date('Y-m-d'), $quote['total_amount'], $userId, date('Y-m-d H:i:s')]
            );
            
            $poId = $this->db->connection()->lastInsertId();

            return (int) $poId;
        });
    }
}