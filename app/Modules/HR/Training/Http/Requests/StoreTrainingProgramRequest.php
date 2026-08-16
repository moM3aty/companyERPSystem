<?php
// Path: app/Modules/HR/Training/Http/Requests/StoreTrainingProgramRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Training\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Min;
use App\Core\Exceptions\ValidationException;

class StoreTrainingProgramRequest
{
    public function validate(array $data): array
    {
        $rules = [
            'title'            => [new Required(), new StringRule()],
            'description'      => [new StringRule()],
            'instructor_name'  => [new Required(), new StringRule()],
            'start_date'       => [new Required(), new Date('Y-m-d')],
            'end_date'         => [new Required(), new Date('Y-m-d')],
            'max_participants' => [new Required(), new Numeric(), new Min(1)],
            'budget'           => [new Numeric(), new Min(0)],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if ($validated['end_date'] < $validated['start_date']) {
            throw new ValidationException(['end_date' => ['Training end date cannot be before the start date.']]);
        }

        return $validated;
    }
}