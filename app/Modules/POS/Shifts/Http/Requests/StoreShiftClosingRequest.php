<?php
// Path: app/Modules/POS/Shifts/Http/Requests/StoreShiftClosingRequest.php

declare(strict_types=1);

namespace App\Modules\POS\Shifts\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;

class StoreShiftClosingRequest
{
    public function validate(array $data): array
    {
        $rules = [
            'actual_cash_counted' => [new Required(), new Numeric(), new Min(0)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}