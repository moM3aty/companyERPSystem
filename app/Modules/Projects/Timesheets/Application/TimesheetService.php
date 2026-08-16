<?php
// Path: app/Modules/Projects/Timesheets/Application/TimesheetService.php

declare(strict_types=1);

namespace App\Modules\Projects\Timesheets\Application;

use App\Modules\Projects\Timesheets\Domain\TimesheetRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

class TimesheetService
{
    protected TimesheetRepositoryInterface $timesheetRepo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;

    public function __construct(
        TimesheetRepositoryInterface $timesheetRepo, 
        TransactionManager $transaction,
        DatabaseManager $db
    ) {
        $this->timesheetRepo = $timesheetRepo;
        $this->transaction = $transaction;
        $this->db = $db;
    }

    public function logTime(array $data, int $companyId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $data['company_id'] = $companyId;
            $data['status']     = 'submitted'; // Auto submit for the flow
            $data['created_at'] = date('Y-m-d H:i:s');

            $timesheetId = $this->timesheetRepo->create($data);

            // تحديث العدد الإجمالي للساعات في المهمة (Task)
            $taskId = (int) $data['task_id'];
            $totalHours = $this->timesheetRepo->getTotalHoursForTask($taskId);

            $this->db->connection()->update(
                "UPDATE project_tasks SET logged_hours = ?, updated_at = ? WHERE id = ?",
                [$totalHours, date('Y-m-d H:i:s'), $taskId]
            );

            return $timesheetId;
        });
    }
}