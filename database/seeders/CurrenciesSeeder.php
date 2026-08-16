<?php
// Path: database/seeders/CurrenciesSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Database\Seeder;

/**
 * Enterprise Currencies Seeder
 * Injects standard ISO Currencies.
 */
class CurrenciesSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'is_base' => 1],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'ر.س', 'is_base' => 0],
            ['code' => 'EGP', 'name' => 'Egyptian Pound', 'symbol' => 'ج.م', 'is_base' => 0],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'is_base' => 0],
        ];

        foreach ($currencies as $currency) {
            $exists = $this->db->connection()->selectOne("SELECT id FROM currencies WHERE code = ?", [$currency['code']]);

            if (!$exists) {
                $this->db->connection()->insert(
                    "INSERT INTO currencies (code, name, symbol, is_base, is_active, created_at) VALUES (?, ?, ?, ?, 1, ?)",
                    [$currency['code'], $currency['name'], $currency['symbol'], $currency['is_base'], date('Y-m-d H:i:s')]
                );
            }
        }
    }
}