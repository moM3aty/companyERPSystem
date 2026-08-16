<?php
// Path: app/Modules/Administration/Branches/Http/Requests/StoreBranchRequest.php

declare(strict_types=1);

namespace App\Modules\Administration\Branches\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Unique;
use App\Core\Database\DatabaseManager;

class StoreBranchRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'code'    => [new Required(), new StringRule(), new Max(50), new Unique($this->db, 'branches', 'code', null, $companyId)],
            'name'    => [new Required(), new StringRule(), new Max(255)],
            'address' => [new Required(), new StringRule()],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}