<?php
// Path: app/Modules/Intercompany/Models/IntercompanyAgreement.php

declare(strict_types=1);

namespace App\Modules\Intercompany\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Intercompany Agreement
 * يمثل اتفاقية تسعير وشروط تبادل مالي بين شركتين شقيقتين (مثال: شركة أ تبيع لشركة ب بهامش ربح 5% فقط).
 */
class IntercompanyAgreement extends BaseModel
{
    use HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'from_company_id'  => 'integer', // الشركة البائعة أو المقدمة للخدمة
        'to_company_id'    => 'integer', // الشركة المشترية
        'markup_percentage'=> 'float',   // هامش الربح المتفق عليه
        'default_ap_account_id' => 'integer',
        'default_ar_account_id' => 'integer',
        'is_active'        => 'boolean',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}