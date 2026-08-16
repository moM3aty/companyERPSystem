<?php
// Path: app/Modules/HR/Training/Domain/TrainingRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\Training\Domain;

use App\Core\Contracts\RepositoryInterface;

interface TrainingRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق من عدد الموظفين المسجلين حالياً في الدورة التدريبية.
     */
    public function getEnrollmentCount(int $programId): int;

    /**
     * تسجيل موظف في الدورة.
     */
    public function enrollEmployee(int $programId, int $employeeId): void;
}