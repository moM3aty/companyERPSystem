<?php
// Path: app/Modules/Accounting/Core/AccountingPeriodService.php

declare(strict_types=1);

namespace App\Modules\Accounting\Core;

use App\Core\Database\DatabaseManager;
use PDO;
use Exception;
use RuntimeException;

/**
 * Enterprise Domain Service: Accounting Period Service
 * Handles validation and state management of Fiscal Years and Periods.
 */
class AccountingPeriodService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseManager::getConnection();
    }

    /**
     * Check if a specific date falls within an OPEN fiscal period.
     * Throws an exception if closed to stop transaction processing instantly.
     *
     * @param int $companyId
     * @param string $date (Format: YYYY-MM-DD)
     * @return bool
     * @throws RuntimeException
     */
    public function validateDateIsOpen(int $companyId, string $date): bool
    {
        $stmt = $this->db->prepare("
            SELECT fp.status AS period_status, fy.status AS year_status
            FROM fiscal_periods fp
            JOIN fiscal_years fy ON fp.fiscal_year_id = fy.id
            WHERE fy.company_id = :company_id
              AND :tx_date BETWEEN fp.start_date AND fp.end_date
            LIMIT 1
        ");

        $stmt->execute([
            ':company_id' => $companyId,
            ':tx_date'    => $date
        ]);

        $result = $stmt->fetch();

        if (!$result) {
            throw new RuntimeException("Transaction Date {$date} does not fall into any defined fiscal period.");
        }

        if ($result['year_status'] === 'closed') {
            throw new RuntimeException("The Fiscal Year for date {$date} is permanently closed.");
        }

        if ($result['period_status'] === 'closed') {
            throw new RuntimeException("The Fiscal Period for date {$date} is closed. Postings are not allowed.");
        }

        return true;
    }

    /**
     * Retrieve the active period ID for a given date.
     */
    public function getActivePeriodId(int $companyId, string $date): ?int
    {
        $stmt = $this->db->prepare("
            SELECT fp.id 
            FROM fiscal_periods fp
            JOIN fiscal_years fy ON fp.fiscal_year_id = fy.id
            WHERE fy.company_id = :company_id
              AND :tx_date BETWEEN fp.start_date AND fp.end_date
              AND fp.status = 'open'
              AND fy.status = 'open'
            LIMIT 1
        ");
        
        $stmt->execute([':company_id' => $companyId, ':tx_date' => $date]);
        $id = $stmt->fetchColumn();

        return $id ? (int) $id : null;
    }
}