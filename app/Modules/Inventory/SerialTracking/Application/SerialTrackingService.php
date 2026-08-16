<?php
// Path: app/Modules/Inventory/SerialTracking/Application/SerialTrackingService.php

declare(strict_types=1);

namespace App\Modules\Inventory\SerialTracking\Application;

use App\Modules\Inventory\SerialTracking\Domain\ItemSerialRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Application Service: Serial Tracking
 * يدير إدخال وصرف الأرقام التسلسلية من وإلى المخزن، ويمنع بيع قطعة غير موجودة.
 */
class SerialTrackingService
{
    protected ItemSerialRepositoryInterface $serialRepo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;

    public function __construct(
        ItemSerialRepositoryInterface $serialRepo, 
        TransactionManager $transaction,
        DatabaseManager $db
    ) {
        $this->serialRepo = $serialRepo;
        $this->transaction = $transaction;
        $this->db = $db;
    }

    /**
     * تسجيل استلام سيريال جديد (IN).
     */
    public function receiveSerial(string $serialNumber, int $productId, int $warehouseId, int $companyId): int
    {
        if ($this->serialRepo->existsForProduct($serialNumber, $productId, $companyId)) {
            throw new BusinessException("Serial number [{$serialNumber}] already exists for this product.", 409);
        }

        return $this->serialRepo->create([
            'company_id'    => $companyId,
            'product_id'    => $productId,
            'warehouse_id'  => $warehouseId,
            'serial_number' => $serialNumber,
            'status'        => 'in_stock',
            'received_at'   => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * بيع/صرف سيريال معين (OUT).
     */
    public function issueSerial(string $serialNumber, int $productId, int $warehouseId, int $companyId): void
    {
        $this->transaction->execute(function () use ($serialNumber, $productId, $warehouseId, $companyId) {
            
            // Pessimistic Lock لمنع بيع القطعة مرتين في نفس اللحظة عبر كاشيرين مختلفين
            $sql = "SELECT id, status FROM inventory_serials 
                    WHERE serial_number = ? AND product_id = ? AND warehouse_id = ? AND company_id = ? 
                    FOR UPDATE";
                    
            $serial = $this->db->connection()->selectOne($sql, [$serialNumber, $productId, $warehouseId, $companyId]);

            if (!$serial) {
                throw new BusinessException("Serial number [{$serialNumber}] not found in this warehouse.", 404);
            }

            if ($serial['status'] !== 'in_stock') {
                throw new BusinessException("Cannot issue serial [{$serialNumber}] as its current status is '{$serial['status']}'.", 422);
            }

            $this->serialRepo->update((int) $serial['id'], [
                'status'  => 'sold',
                'sold_at' => date('Y-m-d H:i:s'),
            ]);
        });
    }
}