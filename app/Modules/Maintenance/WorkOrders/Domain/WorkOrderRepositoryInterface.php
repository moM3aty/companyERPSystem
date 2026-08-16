<?php
// Path: app/Modules/Maintenance/WorkOrders/Domain/WorkOrderRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Maintenance\WorkOrders\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Work Order
 */
interface WorkOrderRepositoryInterface extends RepositoryInterface
{
    
    /**
     * توليد رقم متسلسل لأمر العمل.
     *
     * @param int $companyId
     * @return string
     */
    public function generateWorkOrderNumber(int $companyId): string;
}