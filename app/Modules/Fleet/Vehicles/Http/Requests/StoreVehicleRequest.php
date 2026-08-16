<?php
// Path: app/Modules/Fleet/Vehicles/Http/Requests/StoreVehicleRequest.php

declare(strict_types=1);

namespace App\Modules\Fleet\Vehicles\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Vehicle
 */
class StoreVehicleRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'plate_number'    => [new Required(), new StringRule(), new Unique($this->db, 'fleet_vehicles', 'plate_number', null, $companyId)],
            'make'            => [new Required(), new StringRule()],
            'model'           => [new Required(), new StringRule()],
            'year'            => [new Required(), new Numeric(), new Min(1980)],
            'chassis_number'  => [new StringRule()],
            'driver_id'       => [new Exists($this->db, 'hr_employees', 'id', $companyId)],
            'current_mileage' => [new Numeric(), new Min(0)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}