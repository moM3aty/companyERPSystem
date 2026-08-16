<?php
// Path: app/Modules/Projects/Timesheets/Http/Requests/StoreTimesheetRequest.php

declare(strict_types=1);

namespace App\Modules\Projects\Timesheets\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\StringRule;
use App\Core\Database\DatabaseManager;

class StoreTimesheetRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'project_id'  => [new Required(), new Exists($this->db, 'projects', 'id', $companyId)],
            'task_id'     => [new Required(), new Exists($this->db, 'project_tasks', 'id')], // Tasks are global to projects
            'employee_id' => [new Required(), new Exists($this->db, 'hr_employees', 'id', $companyId)],
            'log_date'    => [new Required(), new Date('Y-m-d')],
            // الحماية من إدخال ساعات عمل خيالية تفوق اليوم
            'hours'       => [new Required(), new Numeric(), new Min(0.1), new Max(24.0)], 
            'description' => [new Required(), new StringRule(), new Max(500)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}