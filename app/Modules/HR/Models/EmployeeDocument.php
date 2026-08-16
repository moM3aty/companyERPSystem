<?php
// Path: app/Modules/HR/Models/EmployeeDocument.php
declare(strict_types=1);

namespace App\Modules\HR\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Employee Document
 * وثيقة رسمية تخص الموظف (إقامة، جواز سفر، شهادة صحية) وتستخدم لتنبيهات انتهاء الصلاحية.
 */
class EmployeeDocument extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'employee_id'      => 'integer',
        'document_type'    => 'string', // 'passport', 'visa', 'id_card', 'contract'
        'document_number'  => 'string',
        'issue_date'       => 'string',
        'expiry_date'      => 'string',
        'file_reference'   => 'string', // مسار الملف في الـ Storage
        'is_verified'      => 'boolean',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}