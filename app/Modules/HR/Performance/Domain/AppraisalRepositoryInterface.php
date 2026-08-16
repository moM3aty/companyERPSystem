<?php
// Path: app/Modules/HR/Performance/Domain/AppraisalRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\Performance\Domain;

use App\Core\Contracts\RepositoryInterface;

interface AppraisalRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق من عدم وجود تقييم آخر لنفس الموظف في نفس الفترة الزمنية.
     */
    public function hasOverlappingAppraisal(int $employeeId, string $periodStart, string $periodEnd, int $companyId): bool;
}