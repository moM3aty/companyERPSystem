<?php
// Path: app/Modules/Inventory/Validators/SerialValidator.php

declare(strict_types=1);

namespace App\Modules\Inventory\Validators;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Validator: Serial Number
 * يحمي النظام من إدخال أرقام تسلسلية مكررة أو صرف رقم تسلسلي مباع مسبقاً.
 */
class SerialValidator
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @throws BusinessException
     */
    public function validateForSale(string $serialNumber, int $productId, int $companyId): void
    {
        $serial = $this->db->connection()->selectOne(
            "SELECT status FROM inventory_serials WHERE serial_number = ? AND product_id = ? AND company_id = ?",
            [$serialNumber, $productId, $companyId]
        );

        if (!$serial) {
            throw new BusinessException("Serial number [{$serialNumber}] does not exist in inventory.", 404);
        }

        if ($serial['status'] !== 'in_stock') {
            throw new BusinessException("Serial number [{$serialNumber}] cannot be sold. Current status: {$serial['status']}.", 422);
        }
    }
}