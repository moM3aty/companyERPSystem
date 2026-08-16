<?php
// Path: app/Modules/Inventory/Products/Application/ProductService.php

declare(strict_types=1);

namespace App\Modules\Inventory\Products\Application;

use App\Modules\Inventory\Products\Domain\Product;
use App\Modules\Inventory\Products\Domain\ProductRepositoryInterface;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Product
 */
class ProductService
{
    protected ProductRepositoryInterface $repository;
    protected TransactionManager $transaction;

    public function __construct(ProductRepositoryInterface $repository, TransactionManager $transaction)
    {
        $this->repository = $repository;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء صنف جديد بشكل آمن.
     *
     * @param array $data
     * @param int $companyId
     * @return Product
     * @throws \Throwable
     */
    public function createProduct(array $data, int $companyId): Product
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $data['company_id'] = $companyId;
            $data['is_active'] = $data['is_active'] ?? 1;
            $data['track_batches'] = $data['track_batches'] ?? 0;
            $data['track_serials'] = $data['track_serials'] ?? 0;

            // إذا كان الصنف خدمياً، لا يمكن تتبع أرقام تشغيلاته أو مسلسلاته
            if ($data['type'] === 'service') {
                $data['track_batches'] = 0;
                $data['track_serials'] = 0;
            }

            $productId = $this->repository->create($data);

            $this->repository->setTenantId($companyId);
            $productData = $this->repository->findOrFail($productId);

            return new Product($productData);
        });
    }
}