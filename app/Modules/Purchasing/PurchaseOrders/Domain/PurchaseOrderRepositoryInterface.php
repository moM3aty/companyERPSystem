<?php
// Path: app/Modules/Purchasing/PurchaseOrders/Domain/PurchaseOrderRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Purchasing\PurchaseOrders\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Purchase Order
 */
interface PurchaseOrderRepositoryInterface extends RepositoryInterface
{
    /**
     * توليد رقم متسلسل لأمر الشراء.
     *
     * @param int $companyId
     * @return string
     */
    public function generatePoNumber(int $companyId): string;

    /**
     * حفظ السطور بشكل مجمع لزيادة الأداء.
     *
     * @param int $poId
     * @param array $items
     * @return void
     */
    public function bulkInsertItems(int $poId, array $items): void;
}