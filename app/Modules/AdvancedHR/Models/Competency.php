<?php
// Path: app/Modules/AdvancedHR/Models/Competency.php

declare(strict_types=1);

namespace App\Modules\AdvancedHR\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;

/**
 * Enterprise Domain Entity: Competency (Skill / Capability)
 * يمثل الجدارات (مهارات، شهادات، قدرات قيادية) المطلوبة في الشركة.
 */
class Competency extends BaseModel
{
    use HasTenant;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'name'        => 'string', // e.g., 'Financial Analysis', 'Leadership'
        'category'    => 'string', // e.g., 'Technical', 'Soft Skill', 'Managerial'
        'description' => 'string',
        'is_active'   => 'boolean',
    ];
}