<?php
// Path: app/Modules/Payroll/PayrollRuns/Http/Requests/StorePayrollRunRequest.php

declare(strict_types=1);

namespace App\Modules\Payroll\PayrollRuns\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Regex;

/**
 * Enterprise Request Validation: Store Payroll Run
 */
class StorePayrollRunRequest
{
    public function validate(array $data): array
    {
        $rules = [
            // يضمن أن الفترة بصيغة صحيحة (مثال: 2026-08)
            'run_period' => [new Required(), new Regex('/^20\d{2}-(0[1-9]|1[0-2])$/')],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}