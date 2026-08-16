<?php
// Path: app/Security/DeviceManagement/Application/DeviceTrustService.php

declare(strict_types=1);

namespace App\Security\DeviceManagement\Application;

use App\Security\DeviceManagement\Domain\UserDeviceRepositoryInterface;
use App\Security\DeviceManagement\Domain\UserDevice;
use App\Security\DeviceManagement\Domain\Events\NewDeviceRegisteredEvent;
use App\Core\Events\EventBus;
use App\Core\Exceptions\AuthenticationException;

/**
 * Enterprise Device Trust Service
 * يراقب الأجهزة. إذا سجل المستخدم الدخول بجهاز جديد، يتم تسجيله وإطلاق إنذار أمني.
 * كما يتحقق مما إذا كان الجهاز الحالي قد تم سحب الثقة منه (Revoked).
 */
class DeviceTrustService
{
    protected UserDeviceRepositoryInterface $deviceRepo;
    protected EventBus $eventBus;

    public function __construct(UserDeviceRepositoryInterface $deviceRepo, EventBus $eventBus)
    {
        $this->deviceRepo = $deviceRepo;
        $this->eventBus = $eventBus;
    }

    public function verifyAndTrackDevice(int $userId, string $deviceId, string $deviceName, string $ipAddress): UserDevice
    {
        $device = $this->deviceRepo->findByDeviceFingerprint($userId, $deviceId);

        if ($device) {
            // الجهاز موجود، نتأكد إنه غير مسحوب الثقة
            if ($device->isRevoked()) {
                throw new AuthenticationException("Access Denied: This device has been revoked for security reasons.", 403);
            }

            // تحديث آخر ظهور
            $this->deviceRepo->touchDevice((int) $device->getAttribute('id'), $ipAddress);
            return $device;
        }

        // الجهاز جديد كلياً
        $newDeviceId = $this->deviceRepo->create([
            'user_id'        => $userId,
            'device_id'      => $deviceId,
            'device_name'    => $deviceName,
            'ip_address'     => $ipAddress,
            'last_active_at' => date('Y-m-d H:i:s'),
            'is_trusted'     => 0, // يحتاج توثيق لاحقاً (مثلاً عبر كود إيميل)
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        $newDevice = $this->deviceRepo->findOrFail($newDeviceId);

        // إطلاق حدث تنبيه أمني للمستخدم
        $this->eventBus->publish(new NewDeviceRegisteredEvent($userId, $deviceName, $ipAddress));

        return new UserDevice($newDevice->toArray());
    }
}