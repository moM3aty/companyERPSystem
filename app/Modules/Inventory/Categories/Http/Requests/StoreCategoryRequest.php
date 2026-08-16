<?php
// Path: app/Modules/Inventory/Categories/Http/Requests/StoreCategoryRequest.php

declare(strict_types=1);

namespace App\Modules\Inventory\Categories\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

class StoreCategoryRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'name'      => [new Required(), new StringRule()],
            'code'      => [new StringRule()],
            'parent_id' => [new Exists($this->db, 'product_categories', 'id', $companyId)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}