<?php
// Path: app/Modules/Projects/Models/ProjectInvoice.php
declare(strict_types=1);

namespace App\Modules\Projects\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Project Invoice
 * الفاتورة الخاصة بالمشروع والتي تُصدر بناءً على المراحل המكتملة (Milestones) أو الساعات المسجلة (Timesheets).
 */
class ProjectInvoice extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'project_id'       => 'integer',
        'sales_invoice_id' => 'integer', // ارتباطها بالفاتورة المالية في دفتر الأستاذ
        'invoice_amount'   => 'float',
        'billing_type'     => 'string', // 'milestone', 'timesheet', 'fixed'
        'status'           => 'string', // 'draft', 'issued', 'paid'
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}