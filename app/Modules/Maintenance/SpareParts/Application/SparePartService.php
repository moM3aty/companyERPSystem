<?php
// Path: app/Modules/Maintenance/SpareParts/Application/SparePartService.php

declare(strict_types=1);

namespace App\Modules\Maintenance\SpareParts\Application;

use App\Modules\Maintenance\SpareParts\Infrastructure\SparePartRepository;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class SparePartService
{
    protected SparePartRepository $sparePartRepo;
    protected TransactionManager $transaction;

    public function __construct(SparePartRepository $sparePartRepo, TransactionManager $transaction)
    {
        $this->sparePartRepo = $sparePartRepo;
        $this->transaction = $transaction;
    }

    /**
     * استهلاك قطعة غيار داخل أمر عمل.
     *
     * @param int $sparePartId
     * @param float $quantity
     * @param int $companyId
     * @return float التكلفة الإجمالية للقطعة المستهلكة
     * @throws BusinessException
     */
    public function consumePart(int $sparePartId, float $quantity, int $companyId): float
    {
        return $this->transaction->execute(function () use ($sparePartId, $quantity, $companyId) {
            $this->sparePartRepo->setTenantId($companyId);
            $part = $this->sparePartRepo->findOrFail($sparePartId);

            $currentStock = (float) $part['stock_quantity'];
            
            if ($currentStock < $quantity) {
                throw new BusinessException("Insufficient spare part stock. Available: {$currentStock}, Requested: {$quantity}");
            }

            // خصم الكمية من ورشة الصيانة
            $this->sparePartRepo->update($sparePartId, [
                'stock_quantity' => $currentStock - $quantity,
                'updated_at'     => date('Y-m-d H:i:s')
            ]);

            // إرجاع تكلفة الاستهلاك لترحل إلى الـ Work Order
            return $quantity * (float) $part['unit_cost'];
        });
    }
}