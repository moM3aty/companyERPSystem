<?php
// Path: app/Security/DeviceManagement/Domain/UserDeviceRepositoryInterface.php

declare(strict_types=1);

namespace App\Security\DeviceManagement\Domain;

use App\Core\Contracts\RepositoryInterface;

interface UserDeviceRepositoryInterface extends RepositoryInterface
{
    /**
     * البحث عن جهاز معين لمستخدم محدد بواسطة بصمة الجهاز.
     */
    public function findByDeviceFingerprint(int $userId, string $deviceId): ?UserDevice;

    /**
     * جلب كافة الأجهزة النشطة لمستخدم معين.
     */
    public function getActiveDevices(int $userId): array;

    /**
     * تسجيل نشاط الجهاز (تحديث وقت آخر ظهور وتحديث الـ IP).
     */
    public function touchDevice(int $id, string $ipAddress): void;
}