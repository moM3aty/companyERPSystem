<?php
// Path: app/Modules/Projects/Timesheets/Domain/TimesheetRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Projects\Timesheets\Domain;

use App\Core\Contracts\RepositoryInterface;

interface TimesheetRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب إجمالي الساعات المسجلة لمهمة محددة.
     *
     * @param int $taskId
     * @return float
     */
    public function getTotalHoursForTask(int $taskId): float;
}