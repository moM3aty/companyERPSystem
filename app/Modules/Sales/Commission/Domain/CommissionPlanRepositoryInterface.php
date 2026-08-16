<?php
// Path: app/Modules/Sales/Commission/Domain/CommissionPlanRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Sales\Commission\Domain;

use App\Core\Contracts\RepositoryInterface;

interface CommissionPlanRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب خطة العمولات النشطة الخاصة بمندوب المبيعات.
     */
    public function getActivePlanForEmployee(int $employeeId, int $companyId): ?array;
}