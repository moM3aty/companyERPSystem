<?php
// Path: app/Modules/CRM/Requests/StoreCampaignRequest.php

declare(strict_types=1);

namespace App\Modules\CRM\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\In;
use App\Core\Exceptions\ValidationException;

class StoreCampaignRequest
{
    public function validate(array $data): array
    {
        $rules = [
            'name'             => [new Required(), new StringRule()],
            'type'             => [new Required(), new In(['email', 'social_media', 'event', 'telemarketing'])],
            'start_date'       => [new Required(), new Date('Y-m-d')],
            'end_date'         => [new Required(), new Date('Y-m-d')],
            'budget'           => [new Numeric(), new Min(0)],
            'expected_revenue' => [new Numeric(), new Min(0)],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if ($validated['end_date'] < $validated['start_date']) {
            throw new ValidationException(['end_date' => ['End date cannot be before start date.']]);
        }

        return $validated;
    }
}