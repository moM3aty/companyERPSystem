<?php
// Path: app/Security/SessionManagement/Application/SessionRevocationService.php

declare(strict_types=1);

namespace App\Security\SessionManagement\Application;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Session Revocation Service (The Kill Switch)
 * يسمح للأدمن أو للنظام بإنهاء جلسات المستخدمين (Logout) عن بُعد وإبطال أجهزتهم.
 */
class SessionRevocationService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * إنهاء جلسة جهاز معين لمستخدم.
     */
    public function revokeDeviceAccess(int $userId, string $deviceId): void
    {
        $this->db->connection()->update(
            "UPDATE security_user_devices SET revoked_at = ? WHERE user_id = ? AND device_id = ?",
            [date('Y-m-d H:i:s'), $userId, $deviceId]
        );
    }

    /**
     * إنهاء كافة جلسات المستخدم (في حال تم اختراق حسابه).
     */
    public function revokeAllSessions(int $userId): void
    {
        $this->db->connection()->update(
            "UPDATE security_user_devices SET revoked_at = ? WHERE user_id = ? AND revoked_at IS NULL",
            [date('Y-m-d H:i:s'), $userId]
        );
    }

    /**
     * قتل أقدم الجلسات للحفاظ على حد التزامن (Concurrent Limit).
     */
    public function revokeOldestSessions(int $userId, int $keepMaxActive): void
    {
        // جلب معرفات الأجهزة القديمة التي تتجاوز الحد
        $sql = "SELECT id FROM security_user_devices 
                WHERE user_id = ? AND revoked_at IS NULL 
                ORDER BY last_active_at DESC 
                LIMIT 100 OFFSET ?";

        $oldDevices = $this->db->connection()->select($sql, [$userId, $keepMaxActive]);

        if (empty($oldDevices)) {
            return;
        }

        $idsToRevoke = array_column($oldDevices, 'id');
        $placeholders = implode(',', array_fill(0, count($idsToRevoke), '?'));

        $this->db->connection()->update(
            "UPDATE security_user_devices SET revoked_at = ? WHERE id IN ({$placeholders})",
            array_merge([date('Y-m-d H:i:s')], $idsToRevoke)
        );
    }
}