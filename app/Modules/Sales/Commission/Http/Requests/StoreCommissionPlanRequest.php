<?php
// Path: app/Modules/Sales/Commission/Http/Requests/StoreCommissionPlanRequest.php

declare(strict_types=1);

namespace App\Modules\Sales\Commission\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Boolean;

class StoreCommissionPlanRequest
{
    public function validate(array $data): array
    {
        $rules = [
            'name'          => [new Required(), new StringRule()],
            'type'          => [new Required(), new In(['percentage', 'fixed_per_invoice'])],
            'value'         => [new Required(), new Numeric(), new Min(0)],
            'target_amount' => [new Numeric(), new Min(0)],
            'is_active'     => [new Boolean()],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}