<?php
// Path: app/Security/DeviceManagement/Domain/UserDevice.php

declare(strict_types=1);

namespace App\Security\DeviceManagement\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Security Entity: User Device
 * يسجل الأجهزة والمتصفحات التي يسجل المستخدم الدخول منها.
 * يستخدم للـ Device Trust ولإلغاء الجلسات عن بُعد (Session Revocation).
 */
class UserDevice extends BaseModel
{
    use HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'user_id'          => 'integer',
        'device_id'        => 'string',  // Unique fingerprint of the device/browser
        'device_name'      => 'string',  // e.g., 'iPhone 13 - Safari'
        'ip_address'       => 'string',
        'last_active_at'   => 'string',
        'is_trusted'       => 'boolean', // هل قام المستخدم بتوثيق هذا الجهاز؟
        'revoked_at'       => 'string',  // إذا تم الإلغاء، يتم طرد المستخدم فوراً
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];

    /**
     * التحقق مما إذا كان الجهاز محظوراً أو تم إلغاء جلسته.
     */
    public function isRevoked(): bool
    {
        return $this->getAttribute('revoked_at') !== null;
    }
}