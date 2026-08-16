<?php
// Path: app/Modules/HR/Employees/Http/Requests/StoreEmployeeRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Employees\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Email;
use App\Core\Validation\Rules\Max;
use App\Core\Validation\Rules\Unique;
use App\Core\Validation\Rules\Exists;
use App\Core\Validation\Rules\Date;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Request Validation: Store Employee
 * يضمن صحة بيانات الموظف (الإيميل، الهوية، الأقسام) قبل إرسالها للـ Service.
 */
class StoreEmployeeRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'first_name'    => [new Required(), new StringRule(), new Max(100)],
            'last_name'     => [new Required(), new StringRule(), new Max(100)],
            'email'         => [new Required(), new Email(), new Unique($this->db, 'hr_employees', 'email', null, $companyId)],
            'national_id'   => [new Required(), new StringRule(), new Unique($this->db, 'hr_employees', 'national_id', null, $companyId)],
            'phone'         => [new Required(), new StringRule(), new Max(50)],
            'hire_date'     => [new Required(), new Date('Y-m-d')],
            'department_id' => [new Required(), new Exists($this->db, 'organization_nodes', 'id', $companyId)],
            'branch_id'     => [new Exists($this->db, 'branches', 'id', $companyId)],
            'manager_id'    => [new Exists($this->db, 'hr_employees', 'id', $companyId)],
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}