<?php
// Path: database/seeders/DefaultSettingsSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Database\Seeder;

/**
 * Enterprise Seeder: Default Settings
 * يحقن الإعدادات الأساسية للنظام (Global Settings) لكي يعمل النظام فور التثبيت.
 */
class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['scope' => 'global', 'key' => 'app.name', 'value' => 'ERP Pro Enterprise', 'type' => 'string'],
            ['scope' => 'global', 'key' => 'app.timezone', 'value' => 'Africa/Cairo', 'type' => 'string'],
            ['scope' => 'global', 'key' => 'accounting.default_currency', 'value' => 'USD', 'type' => 'string'],
            ['scope' => 'global', 'key' => 'security.session_timeout', 'value' => '120', 'type' => 'integer'], // Minutes
            ['scope' => 'global', 'key' => 'security.password_expiry_days', 'value' => '90', 'type' => 'integer'],
            ['scope' => 'global', 'key' => 'ui.default_theme', 'value' => 'light', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            $exists = $this->db->connection()->selectOne(
                "SELECT id FROM settings WHERE scope = ? AND `key` = ? AND scope_id IS NULL",
                [$setting['scope'], $setting['key']]
            );

            if (!$exists) {
                $this->db->connection()->insert(
                    "INSERT INTO settings (scope, scope_id, `key`, `value`, `type`, created_at, updated_at) VALUES (?, NULL, ?, ?, ?, ?, ?)",
                    [
                        $setting['scope'], 
                        $setting['key'], 
                        json_encode($setting['value'], JSON_UNESCAPED_UNICODE), 
                        $setting['type'], 
                        date('Y-m-d H:i:s'), 
                        date('Y-m-d H:i:s')
                    ]
                );
            }
        }
    }
}