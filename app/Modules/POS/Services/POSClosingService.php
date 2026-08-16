<?php
// Path: app/Modules/POS/Services/POSClosingService.php

declare(strict_types=1);

namespace App\Modules\POS\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

class POSClosingService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function generateZReport(int $shiftId, float $actualAmount, int $companyId, int $userId): array
    {
        $shift = $this->db->connection()->selectOne("SELECT * FROM pos_shifts WHERE id = ? AND company_id = ?", [$shiftId, $companyId]);
        if (!$shift || $shift['status'] === 'closed') {
            throw new BusinessException("Shift is invalid or already closed.");
        }

        $cashSales = $this->db->connection()->selectOne(
            "SELECT SUM(grand_total) as total FROM pos_orders WHERE shift_id = ? AND payment_method = 'cash' AND status = 'completed'",
            [$shiftId]
        );

        $expectedAmount = (float)$shift['opening_amount'] + (float)($cashSales['total'] ?? 0);
        $difference = round($actualAmount - $expectedAmount, 2);

        // إغلاق الوردية
        $this->db->connection()->update(
            "UPDATE pos_shifts SET status = 'closed', closed_at = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $shiftId]
        );

        // حفظ تقرير الإغلاق (Z-Report)
        $this->db->connection()->insert(
            "INSERT INTO pos_closings (company_id, terminal_id, shift_id, expected_amount, actual_amount, difference, closed_by, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$companyId, $shift['terminal_id'], $shiftId, $expectedAmount, $actualAmount, $difference, $userId, date('Y-m-d H:i:s')]
        );
        $closingId = (int) $this->db->connection()->lastInsertId();

        return [
            'closing_id' => $closingId,
            'expected'   => $expectedAmount,
            'actual'     => $actualAmount,
            'difference' => $difference
        ];
    }
}