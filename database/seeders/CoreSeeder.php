<?php
// Path: database/seeders/CoreSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Database\Seeder;

/**
 * Enterprise Core Seeder
 * The master seeder that orchestrates the execution of all foundational seeders.
 */
class CoreSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Data
        $this->call(CountriesSeeder::class);
        $this->call(CurrenciesSeeder::class);
        
        // 2. Security & Admin
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        
        // 3. Configurations
        $this->call(DefaultSettingsSeeder::class);
    }
}