<?php
// Path: app/Modules/HR/Leaves/Http/Requests/StoreLeaveRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Leaves\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreLeaveRequestRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'employee_id' => [new Required(), new Exists($this->db, 'hr_employees', 'id', $companyId)],
            'leave_type'  => [new Required(), new In(['annual', 'sick', 'unpaid', 'maternity'])],
            'start_date'  => [new Required(), new Date('Y-m-d')],
            'end_date'    => [new Required(), new Date('Y-m-d')],
            'reason'      => [new StringRule()],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if ($validated['end_date'] < $validated['start_date']) {
            throw new ValidationException(['end_date' => ['End date cannot be before start date.']]);
        }

        return $validated;
    }
}