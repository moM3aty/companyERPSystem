<?php
// Path: app/Modules/Sales/Promotions/Domain/PromotionRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Sales\Promotions\Domain;

use App\Core\Contracts\RepositoryInterface;

interface PromotionRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب العروض الترويجية الفعالة لمنتج معين.
     */
    public function getActivePromotionsForProduct(int $productId, int $companyId): array;
}