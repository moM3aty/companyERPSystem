<?php
// Path: app/Modules/Projects/Projects/Http/Requests/StoreProjectRequest.php

declare(strict_types=1);

namespace App\Modules\Projects\Projects\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\ValidationException;

/**
 * Enterprise Request Validation: Store Project
 * يضمن صحة إدخالات المشروع وربطها الصحيح بالعملاء والمستخدمين.
 */
class StoreProjectRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'name'           => [new Required(), new StringRule()],
            'customer_id'    => [new Exists($this->db, 'customers', 'id', $companyId)],
            'manager_id'     => [new Required(), new Exists($this->db, 'users', 'id', $companyId)],
            'cost_center_id' => [new Exists($this->db, 'cost_centers', 'id', $companyId)],
            'start_date'     => [new Required(), new Date('Y-m-d')],
            'end_date'       => [new Date('Y-m-d')],
            'budget'         => [new Numeric(), new Min(0)],
        ];

        $validated = ValidatorFactory::makeAndValidate($data, $rules);

        if (!empty($validated['end_date']) && $validated['end_date'] < $validated['start_date']) {
            throw new ValidationException(['end_date' => ['The end date cannot be before the start date.']]);
        }

        return $validated;
    }
}