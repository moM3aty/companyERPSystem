<?php
// Path: app/Core/Authorization/PermissionManager.php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Permission Manager
 * يدير إدخال وتعديل الصلاحيات الذرية (Atomic Permissions) في قاعدة البيانات.
 * يستخدم من قبل المطورين لإضافة صلاحيات جديدة للنظام عند إضافة موديول جديد.
 */
class PermissionManager
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تسجيل صلاحية جديدة في النظام.
     */
    public function registerPermission(string $module, string $resource, string $action, string $description = ''): void
    {
        $exists = $this->db->connection()->selectOne(
            "SELECT id FROM permissions WHERE module = ? AND resource = ? AND action = ?",
            [$module, $resource, $action]
        );

        if (!$exists) {
            $this->db->connection()->insert(
                "INSERT INTO permissions (module, resource, action, description) VALUES (?, ?, ?, ?)",
                [$module, $resource, $action, $description]
            );
        }
    }
}