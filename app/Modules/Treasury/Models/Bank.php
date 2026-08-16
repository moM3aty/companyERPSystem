<?php
// Path: app/Modules/Treasury/Models/Bank.php

declare(strict_types=1);

namespace App\Modules\Treasury\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Bank
 * يمثل المؤسسة البنكية ذاتها (مثال: البنك الأهلي، بنك الراجحي) ورموز السويفت الخاصة بها.
 */
class Bank extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'         => 'integer',
        'company_id' => 'integer',
        'name'       => 'string',
        'swift_code' => 'string',
        'is_active'  => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];
}