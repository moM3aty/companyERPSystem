<?php
// Path: app/Modules/Maintenance/WorkOrders/Http/Requests/StoreWorkOrderRequest.php

declare(strict_types=1);

namespace App\Modules\Maintenance\WorkOrders\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\In;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Work Order
 */
class StoreWorkOrderRequest
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
            'title'          => [new Required(), new StringRule()],
            'description'    => [new StringRule()],
            'assigned_to'    => [new Exists($this->db, 'hr_employees', 'id', $companyId)],
            'priority'       => [new Required(), new In(['low', 'normal', 'high', 'critical'])],
            'scheduled_date' => [new Required(), new Date('Y-m-d')],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}