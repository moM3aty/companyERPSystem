<?php
// Path: app/Modules/Maintenance/MaintenancePlans/Http/Requests/StoreMaintenancePlanRequest.php

declare(strict_types=1);

namespace App\Modules\Maintenance\MaintenancePlans\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Maintenance Plan
 */
class StoreMaintenancePlanRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'asset_id'       => [new Required(), new Exists($this->db, 'fixed_assets', 'id', $companyId)],
            'name'           => [new Required(), new StringRule()],
            'description'    => [new StringRule()],
            'frequency_days' => [new Required(), new Numeric(), new Min(1)],
            'next_due_date'  => [new Required(), new Date('Y-m-d')],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}