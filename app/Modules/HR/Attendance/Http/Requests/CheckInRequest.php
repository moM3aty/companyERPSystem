<?php
// Path: app/Modules/HR/Attendance/Http/Requests/CheckInRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Attendance\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

class CheckInRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            // إذا تم ربط الجهاز بالـ API، سيرسل Employee ID.
            'employee_id' => [new Required(), new Exists($this->db, 'hr_employees', 'id', $companyId)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}