<?php
// Path: app/Modules/Inventory/Warehouses/Http/RequestsRequests/StoreWarehouseRequest.php

declare(strict_types=1);

namespace App\Modules\Inventory\Warehouses\Http\Requests;

use App\Core\Database\DatabaseManager;
use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Boolean;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\Exists;

/**
 * Enterprise Request Validation: Store Warehouse
 */
class StoreWarehouseRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * التحقق من البيانات.
     *
     * @param array $data
     * @param int $companyId
     * @return array
     * @throws \App\Core\Exceptions\ValidationException
     */
    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'code'        => [new Required(), new StringRule(), new Max(50), new Unique($this->db, 'warehouses', 'code', null, $companyId)],
            'name'        => [new Required(), new StringRule(), new Max(150)],
            'branch_id'   => [new Required(), new Exists($this->db, 'branches', 'id', $companyId)],
            'location_id' => [new Exists($this->db, 'locations', 'id', $companyId)],
            'address'     => [new StringRule(), new Max(255)],
            'is_active'   => [new Boolean()],
            'is_transit'  => [new Boolean()],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}