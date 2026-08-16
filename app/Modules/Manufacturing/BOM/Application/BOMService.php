<?php
// Path: app/Modules/Manufacturing/BOM/Application/BOMService.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\BOM\Application;

use App\Modules\Manufacturing\BOM\Domain\BOMRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: BOM
 * Enforces manufacturing rules (e.g., circular dependencies prevention).
 */
class BOMService
{
    protected BOMRepositoryInterface $bomRepo;
    protected TransactionManager $transaction;

    public function __construct(BOMRepositoryInterface $bomRepo, TransactionManager $transaction)
    {
        $this->bomRepo = $bomRepo;
        $this->transaction = $transaction;
    }

    /**
     * Create a new Bill of Materials.
     *
     * @param array $headerData
     * @param array $itemsData
     * @param int $companyId
     * @return int
     * @throws BusinessException|\Throwable
     */
    public function createBOM(array $headerData, array $itemsData, int $companyId): int
    {
        return $this->transaction->execute(function () use ($headerData, $itemsData, $companyId) {
            
            $finishedGoodId = (int) $headerData['product_id'];

            // 1. Business Logic: Prevent obvious circular dependency (Product cannot be a component of itself)
            foreach ($itemsData as $item) {
                if ((int) $item['component_product_id'] === $finishedGoodId) {
                    throw new BusinessException("A product cannot be a component within its own Bill of Materials.");
                }
            }

            // 2. Prepare Data
            $bomData = [
                'company_id'     => $companyId,
                'product_id'     => $finishedGoodId,
                'code'           => $headerData['code'],
                'name'           => $headerData['name'],
                'batch_quantity' => $headerData['batch_quantity'],
                'is_active'      => 1,
                'created_at'     => date('Y-m-d H:i:s'),
            ];

            // 3. Save via Repository
            return $this->bomRepo->saveWithItems($bomData, $itemsData);
        });
    }
}