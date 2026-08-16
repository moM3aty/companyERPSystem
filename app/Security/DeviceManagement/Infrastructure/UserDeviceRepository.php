<?php
// Path: app/Security/DeviceManagement/Infrastructure/UserDeviceRepository.php

declare(strict_types=1);

namespace App\Security\DeviceManagement\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Security\DeviceManagement\Domain\UserDevice;
use App\Security\DeviceManagement\Domain\UserDeviceRepositoryInterface;

class UserDeviceRepository extends BaseRepository implements UserDeviceRepositoryInterface
{
    protected string $table = 'security_user_devices';
    protected bool $useTenantScope = false; // Devices are linked to users directly
    protected bool $useSoftDeletes = false;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function findByDeviceFingerprint(int $userId, string $deviceId): ?UserDevice
    {
        $data = $this->newQuery()
            ->where('user_id', '=', $userId)
            ->where('device_id', '=', $deviceId)
            ->first();

        return $data ? new UserDevice($data) : null;
    }

    public function getActiveDevices(int $userId): array
    {
        $records = $this->newQuery()
            ->where('user_id', '=', $userId)
            ->whereNull('revoked_at')
            ->orderBy('last_active_at', 'desc')
            ->get();

        return array_map(fn($row) => new UserDevice($row), $records);
    }

    public function touchDevice(int $id, string $ipAddress): void
    {
        $this->update($id, [
            'ip_address' => $ipAddress,
            'last_active_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}