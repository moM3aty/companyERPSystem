<?php
// Path: app/Modules/Projects/Tasks/Http/Requests/StoreTaskRequest.php

declare(strict_types=1);

namespace App\Modules\Projects\Tasks\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Numeric;
use App\Core\Validation\Rules\Min;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Validation\Rules\In;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Project Task
 */
class StoreTaskRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'project_id'      => [new Required(), new Exists($this->db, 'projects', 'id', $companyId)],
            'name'            => [new Required(), new StringRule()],
            'description'     => [new StringRule()],
            'assigned_to'     => [new Required(), new Exists($this->db, 'users', 'id', $companyId)],
            'priority'        => [new Required(), new In(['low', 'normal', 'high', 'urgent'])],
            'estimated_hours' => [new Numeric(), new Min(0.1)],
            'start_date'      => [new Date('Y-m-d')],
            'due_date'        => [new Date('Y-m-d')],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}