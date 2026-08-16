<?php
// Path: app/Modules/Administration/Companies/Http/Requests/StoreCompanyRequest.php

declare(strict_types=1);

namespace App\Modules\Administration\Companies\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Unique;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Company
 */
class StoreCompanyRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data): array
    {
        // Company creation is global, so we pass null for companyId in Unique rules
        $rules = [
            'name'                 => [new Required(), new StringRule(), new Max(255)],
            'registration_number'  => [new Required(), new StringRule(), new Unique($this->db, 'companies', 'registration_number')],
            'tax_number'           => [new StringRule(), new Max(50)],
            'base_currency_id'     => [new Required(), new Exists($this->db, 'currencies', 'id')],
            'timezone'             => [new Required(), new StringRule(), new Max(100)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}