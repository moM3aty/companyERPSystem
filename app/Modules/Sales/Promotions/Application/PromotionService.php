<?php
// Path: app/Modules/Sales/Promotions/Application/PromotionService.php

declare(strict_types=1);

namespace App\Modules\Sales\Promotions\Application;

use App\Modules\Sales\Promotions\Domain\PromotionRepositoryInterface;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Sales Promotions
 */
class PromotionService
{
    protected PromotionRepositoryInterface $promotionRepo;
    protected TransactionManager $transaction;

    public function __construct(PromotionRepositoryInterface $promotionRepo, TransactionManager $transaction)
    {
        $this->promotionRepo = $promotionRepo;
        $this->transaction = $transaction;
    }

    public function createPromotion(array $data, int $companyId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            $data['company_id'] = $companyId;
            $data['is_active'] = $data['is_active'] ?? 1;
            
            // Defaulting values to prevent DB null constraint issues if not provided
            $data['discount_percent'] = $data['discount_percent'] ?? 0.0;
            $data['fixed_price'] = $data['fixed_price'] ?? 0.0;
            
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->promotionRepo->create($data);
        });
    }
}