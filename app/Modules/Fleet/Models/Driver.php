<?php
// Path: app/Modules/Fleet/Models/Driver.php
declare(strict_types=1);

namespace App\Modules\Fleet\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Fleet Driver
 * يمثل السائق وتفاصيل رخصته بمعزل عن ملفه في الموارد البشرية لضمان متابعة تواريخ الانتهاء.
 */
class Driver extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'employee_id'    => 'integer', // الربط بموديول الـ HR
        'license_number' => 'string',
        'license_type'   => 'string', // 'heavy', 'light', 'commercial'
        'license_expiry' => 'string', // YYYY-MM-DD
        'rating'         => 'float',  // تقييم السائق
        'is_active'      => 'boolean',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];

    public function isLicenseValid(): bool
    {
        return $this->getAttribute('license_expiry') >= date('Y-m-d');
    }
}