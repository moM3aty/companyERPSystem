<?php
// Path: app/Modules/Sales/Services/CommissionService.php
declare(strict_types=1);

namespace App\Modules\Sales\Services;

use App\Core\Database\DatabaseManager;

class CommissionService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function calculateCommissionForInvoice(int $invoiceId, int $companyId): void
    {
        $invoice = $this->db->connection()->selectOne(
            "SELECT id, sales_rep_id, grand_total FROM sales_invoices WHERE id = ? AND company_id = ? AND status = 'posted'",
            [$invoiceId, $companyId]
        );

        if (!$invoice || !$invoice['sales_rep_id']) return;

        // افتراض: جلب نسبة عمولة المندوب من ملفه
        $rep = $this->db->connection()->selectOne(
            "SELECT commission_rate FROM employees WHERE id = ? AND company_id = ?",
            [$invoice['sales_rep_id'], $companyId]
        );

        $rate = $rep ? (float)$rep['commission_rate'] : 0.0;
        if ($rate <= 0) return;

        $amount = (float)$invoice['grand_total'] * ($rate / 100);

        $this->db->connection()->insert(
            "INSERT INTO sales_commissions (company_id, sales_invoice_id, sales_rep_id, invoice_amount, commission_rate, commission_amount, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)",
            [$companyId, $invoice['id'], $invoice['sales_rep_id'], $invoice['grand_total'], $rate, $amount, date('Y-m-d H:i:s')]
        );
    }
}