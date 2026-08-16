<?php
// Path: app/Modules/Treasury/Accounts/Domain/TreasuryAccount.php

declare(strict_types=1);

namespace App\Modules\Treasury\Accounts\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Treasury Account
 * يمثل حساب خزينة (Cash) أو حساب بنكي (Bank).
 * يرتبط بشكل وثيق مع حساب في دليل الحسابات (GL Account).
 */
class TreasuryAccount extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'name'           => 'string',
        'type'           => 'string', // 'cash', 'bank'
        'account_number' => 'string', // رقم الحساب البنكي (فارغ للخزينة)
        'currency_id'    => 'integer',
        'gl_account_id'  => 'integer', // حساب الأستاذ العام المرتبط في المحاسبة
        'is_active'      => 'boolean',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];

    /**
     * التحقق مما إذا كان الحساب بنكياً.
     *
     * @return bool
     */
    public function isBank(): bool
    {
        return $this->getAttribute('type') === 'bank';
    }
}