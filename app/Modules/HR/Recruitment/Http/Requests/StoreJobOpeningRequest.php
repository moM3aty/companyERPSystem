<?php
// Path: app/Modules/HR/Recruitment/Http/Requests/StoreJobOpeningRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

class StoreJobOpeningRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'department_id'   => [new Required(), new Exists($this->db, 'organization_nodes', 'id', $companyId)],
            'title'           => [new Required(), new StringRule()],
            'description'     => [new Required(), new StringRule()],
            'requirements'    => [new StringRule()],
            'positions_count' => [new Required(), new Numeric(), new Min(1)],
            'closing_date'    => [new Required(), new Date('Y-m-d')],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}