<?php
// Path: app/Modules/Maintenance/MaintenancePlans/Domain/MaintenancePlanRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Maintenance\MaintenancePlans\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Maintenance Plan
 */
interface MaintenancePlanRepositoryInterface extends RepositoryInterface
{
    
    /**
     * جلب الخطط المستحقة لتوليد أوامر العمل آلياً.
     *
     * @param string $date
     * @param int $companyId
     * @return array
     */
    public function getDuePlans(string $date, int $companyId): array;
}