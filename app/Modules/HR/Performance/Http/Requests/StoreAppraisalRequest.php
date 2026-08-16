<?php
// Path: app/Modules/HR/Performance/Http/Requests/StoreAppraisalRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Performance\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

class StoreAppraisalRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'employee_id'   => [new Required(), new Exists($this->db, 'hr_employees', 'id', $companyId)],
            'period_start'  => [new Required(), new Date('Y-m-d')],
            'period_end'    => [new Required(), new Date('Y-m-d')],
            'overall_score' => [new Required(), new Numeric(), new Min(0), new Max(100)],
            'feedback'      => [new Required(), new StringRule()],
            'goals_achieved'=> ['array'],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if ($validated['period_end'] < $validated['period_start']) {
            throw new ValidationException(['period_end' => ['End date cannot be before start date.']]);
        }

        return $validated;
    }
}