<?php
// Path: app/Modules/Administration/Users/Domain/User.php

declare(strict_types=1);

namespace App\Modules\Administration\Users\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: User
 * يمثل المستخدم في موديول الإدارة. يعتمد على BaseModel لدعم الـ Audit و الـ Dirty State.
 */
class User extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'                    => 'integer',
        'company_id'            => 'integer',
        'username'              => 'string',
        'email'                 => 'string',
        'password_hash'         => 'string',
        'employee_id'           => 'integer',
        'language'              => 'string',
        'timezone'              => 'string',
        'is_active'             => 'boolean',
        'failed_login_attempts' => 'integer',
        'locked_until'          => 'string',
        'created_at'            => 'string',
        'updated_at'            => 'string',
        'deleted_at'            => 'string',
    ];

    /**
     * إخفاء الحقول الحساسة عند تحويل الكائن إلى مصفوفة للـ API.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        unset($array['password_hash']);
        return $array;
    }

    /**
     * التحقق مما إذا كان الحساب مقفولاً.
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        $lockedUntil = $this->getAttribute('locked_until');
        if (!$lockedUntil) {
            return false;
        }

        return strtotime((string) $lockedUntil) > time();
    }
}