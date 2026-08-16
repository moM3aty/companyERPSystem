<?php
// Path: app/Modules/Inventory/Categories/Application/CategoryService.php

declare(strict_types=1);

namespace App\Modules\Inventory\Categories\Application;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\Inventory\Categories\Domain\CategoryRepositoryInterface;
use App\Modules\Inventory\Categories\Domain\ProductCategory;

class CategoryService
{
    protected CategoryRepositoryInterface $categoryRepo;
    protected TransactionManager $transaction;

    public function __construct(CategoryRepositoryInterface $categoryRepo, TransactionManager $transaction)
    {
        $this->categoryRepo = $categoryRepo;
        $this->transaction = $transaction;
    }

    public function createCategory(array $data, int $companyId): ProductCategory
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $data['company_id'] = $companyId;
            $data['level'] = 1;
            $data['is_active'] = 1;

            if (!empty($data['parent_id'])) {
                $this->categoryRepo->setTenantId($companyId);
                $parent = $this->categoryRepo->findOrFail((int) $data['parent_id']);
                
                // منع التعشيق اللانهائي للبيانات (حماية للأداء)
                if ((int) $parent['level'] >= 5) {
                    throw new BusinessException("Cannot create category. Maximum nesting level of 5 reached.");
                }
                
                $data['level'] = (int) $parent['level'] + 1;
            }

            $categoryId = $this->categoryRepo->create($data);
            
            $this->categoryRepo->setTenantId($companyId);
            $categoryData = $this->categoryRepo->findOrFail($categoryId);

            return new ProductCategory($categoryData);
        });
    }
}