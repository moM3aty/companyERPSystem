<?php
// Path: database/seeders/CountriesSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Database\Seeder;

/**
 * Enterprise Countries Seeder
 * Injects standard ISO countries.
 */
class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['code' => 'EG', 'name' => 'Egypt', 'dial_code' => '+20'],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'dial_code' => '+966'],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'dial_code' => '+971'],
            ['code' => 'US', 'name' => 'United States', 'dial_code' => '+1'],
            ['code' => 'GB', 'name' => 'United Kingdom', 'dial_code' => '+44'],
        ];

        foreach ($countries as $country) {
            $exists = $this->db->connection()->selectOne("SELECT id FROM countries WHERE code = ?", [$country['code']]);

            if (!$exists) {
                $this->db->connection()->insert(
                    "INSERT INTO countries (code, name, dial_code, is_active, created_at) VALUES (?, ?, ?, 1, ?)",
                    [$country['code'], $country['name'], $country['dial_code'], date('Y-m-d H:i:s')]
                );
            }
        }
    }
}