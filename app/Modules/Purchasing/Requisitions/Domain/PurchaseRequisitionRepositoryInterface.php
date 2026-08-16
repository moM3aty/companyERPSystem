<?php
// Path: app/Modules/Purchasing/Requisitions/Domain/PurchaseRequisitionRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Requisitions\Domain;

use App\Core\Contracts\RepositoryInterface;

interface PurchaseRequisitionRepositoryInterface extends RepositoryInterface
{
    /**
     * توليد رقم طلب احتياج داخلي متسلسل للشركة.
     *
     * @param int $companyId
     * @return string
     */
    public function generatePrNumber(int $companyId): string;

    /**
     * إدخال عناصر الطلب دفعة واحدة لزيادة الأداء.
     *
     * @param int $prId
     * @param array $items
     * @return void
     */
    public function bulkInsertItems(int $prId, array $items): void;
}