<?php
// Path: app/Modules/Manufacturing/ProductionOrders/Http/Requests/StoreProductionOrderRequest.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionOrders\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Production Order
 */
class StoreProductionOrderRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'bom_id'           => [new Required(), new Exists($this->db, 'manufacturing_boms', 'id', $companyId)],
            'planned_quantity' => [new Required(), new Numeric(), new Min(0.01)],
            'start_date'       => [new Required(), new Date('Y-m-d')],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}