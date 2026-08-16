<?php
// Path: app/Modules/Projects/Projects/Domain/Project.php

declare(strict_types=1);

namespace App\Modules\Projects\Projects\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Project
 * الكيان الأساسي للمشروع. يربط بين العملاء، مركز التكلفة، ومدير المشروع.
 */
class Project extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'branch_id'      => 'integer',
        'code'           => 'string', // e.g., 'PRJ-2026-001'
        'name'           => 'string',
        'customer_id'    => 'integer', // المشروع قد يكون لعميل خارجي
        'manager_id'     => 'integer', // مدير المشروع (User ID)
        'cost_center_id' => 'integer', // مركز التكلفة لربط المصروفات بالمحاسبة
        'status'         => 'string', // 'planned', 'active', 'on_hold', 'completed', 'cancelled'
        'start_date'     => 'string', // YYYY-MM-DD
        'end_date'       => 'string', // YYYY-MM-DD
        'budget'         => 'float',
        'created_at'     => 'string',
        'updated_at'     => 'string',
        'deleted_at'     => 'string',
    ];

    /**
     * التحقق مما إذا كان المشروع نشطاً وقابلاً لتسجيل التكاليف/المهام عليه.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getAttribute('status') === 'active';
    }
}