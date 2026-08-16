<?php
// Path: app/Modules/Fleet/Trips/Http/Requests/StoreTripRequest.php

declare(strict_types=1);

namespace App\Modules\Fleet\Trips\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Trip
 */
class StoreTripRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'vehicle_id'     => [new Required(), new Exists($this->db, 'fleet_vehicles', 'id', $companyId)],
            'driver_id'      => [new Required(), new Exists($this->db, 'hr_employees', 'id', $companyId)],
            'start_location' => [new Required(), new StringRule()],
            'end_location'   => [new Required(), new StringRule()],
            'start_time'     => [new Required(), new Date('Y-m-d H:i:s')],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}