<?php
// Path: app/Modules/HR/Employees/Domain/Employee.php

declare(strict_types=1);

namespace App\Modules\HR\Employees\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Employee
 * الكيان الأساسي للموظف في النظام.
 */
class Employee extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'employee_code'    => 'string',
        'first_name'       => 'string',
        'last_name'        => 'string',
        'email'            => 'string',
        'phone'            => 'string',
        'national_id'      => 'string',
        'department_id'    => 'integer', // يربط بـ organization_nodes
        'manager_id'       => 'integer', // يربط بموظف آخر
        'hire_date'        => 'string',  // YYYY-MM-DD
        'status'           => 'string',  // active, on_leave, terminated, suspended
        'created_at'       => 'string',
        'updated_at'       => 'string',
        'deleted_at'       => 'string',
    ];

    /**
     * الحصول على الاسم بالكامل.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return trim($this->getAttribute('first_name') . ' ' . $this->getAttribute('last_name'));
    }
}