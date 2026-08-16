<?php
// Path: app/Modules/POS/Orders/Domain/PosOrderRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\POS\Orders\Domain;

use App\Core\Contracts\RepositoryInterface;

interface PosOrderRepositoryInterface extends RepositoryInterface
{
    public function generateOrderNumber(int $companyId): string;
    
    /**
     * إدخال عناصر فاتورة الـ POS بشكل مجمع لزيادة الأداء.
     *
     * @param int $orderId
     * @param array $items
     * @return void
     */
    public function bulkInsertItems(int $orderId, array $items): void;
}