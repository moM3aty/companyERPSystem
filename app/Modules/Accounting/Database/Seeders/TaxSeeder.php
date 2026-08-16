<?php
// Path: app/Modules/Accounting/Database/Seeders/TaxSeeder.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Seeders;

use App\Core\Database\DatabaseManager;

class TaxSeeder
{
    public function run(int $companyId): void
    {
        $db = DatabaseManager::getConnection();

        $taxes = [
            ['code' => 'VAT15', 'name' => 'Standard VAT 15%', 'type' => 'VAT', 'rate' => 15.00],
            ['code' => 'VAT0', 'name' => 'Zero Rated 0%', 'type' => 'Zero', 'rate' => 0.00],
            ['code' => 'EXEMPT', 'name' => 'VAT Exempt', 'type' => 'Exempt', 'rate' => 0.00],
        ];

        $stmt = $db->prepare("
            INSERT IGNORE INTO taxes (company_id, code, name, tax_type, rate) 
            VALUES (:cid, :code, :name, :type, :rate)
        ");

        foreach ($taxes as $tax) {
            $stmt->execute([
                ':cid' => $companyId,
                ':code' => $tax['code'],
                ':name' => $tax['name'],
                ':type' => $tax['type'],
                ':rate' => $tax['rate']
            ]);
        }
    }
}