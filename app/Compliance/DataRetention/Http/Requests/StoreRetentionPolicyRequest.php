<?php
// Path: app/Compliance/DataRetention/Http/Requests/StoreRetentionPolicyRequest.php

declare(strict_types=1);

namespace App\Compliance\DataRetention\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Boolean;

class StoreRetentionPolicyRequest
{
    public function validate(array $data): array
    {
        $rules = [
            'entity_type'      => [new Required(), new In(['audit_logs', 'activity_logs', 'failed_jobs', 'integration_logs', 'customers'])],
            'retention_days'   => [new Required(), new Numeric(), new Min(1)], // Minimum 1 day
            'action_on_expiry' => [new Required(), new In(['delete', 'anonymize'])],
            'is_active'        => [new Boolean()],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}