<?php
// Path: app/Modules/HR/Contracts/Http/Requests/StoreContractRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Contracts\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

/**
 * Enterprise Request Validation: Store Contract
 */
class StoreContractRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'employee_id'    => [new Required(), new Exists($this->db, 'hr_employees', 'id', $companyId)],
            'contract_type'  => [new Required(), new In(['full_time', 'part_time', 'freelance'])],
            'start_date'     => [new Required(), new Date('Y-m-d')],
            'end_date'       => [new Date('Y-m-d')],
            'basic_salary'   => [new Required(), new Numeric(), new Min(0)],
            'currency_id'    => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'working_hours'  => [new Numeric(), new Min(1)],
            'probation_days' => [new Numeric(), new Min(0)],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!empty($validated['end_date']) && $validated['end_date'] < $validated['start_date']) {
            throw new ValidationException(['end_date' => ['End date cannot be before start date.']]);
        }

        return $validated;
    }
}