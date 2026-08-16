<?php
// Path: app/Modules/Accounting/Database/Seeders/ChartOfAccountsSeeder.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Seeders;

use App\Core\Database\DatabaseManager;
use PDO;

class ChartOfAccountsSeeder
{
    public function run(int $companyId): void
    {
        $db = DatabaseManager::getConnection();

        $accounts = [
            // Assets
            ['account_code' => '1000', 'account_name' => 'Current Assets', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'level' => 1, 'is_control_account' => 1],
            ['account_code' => '1010', 'account_name' => 'Cash in Bank - Main', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'level' => 2, 'is_control_account' => 0],
            ['account_code' => '1200', 'account_name' => 'Accounts Receivable (A/R)', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'level' => 2, 'is_control_account' => 1],
            ['account_code' => '1300', 'account_name' => 'Inventory Asset', 'account_type' => 'Asset', 'normal_balance' => 'Debit', 'level' => 2, 'is_control_account' => 1],
            
            // Liabilities
            ['account_code' => '2000', 'account_name' => 'Current Liabilities', 'account_type' => 'Liability', 'normal_balance' => 'Credit', 'level' => 1, 'is_control_account' => 1],
            ['account_code' => '2010', 'account_name' => 'Accounts Payable (A/P)', 'account_type' => 'Liability', 'normal_balance' => 'Credit', 'level' => 2, 'is_control_account' => 1],
            ['account_code' => '2200', 'account_name' => 'VAT Payable', 'account_type' => 'Liability', 'normal_balance' => 'Credit', 'level' => 2, 'is_control_account' => 0],
            
            // Equity
            ['account_code' => '3000', 'account_name' => 'Owners Equity', 'account_type' => 'Equity', 'normal_balance' => 'Credit', 'level' => 1, 'is_control_account' => 0],
            ['account_code' => '3100', 'account_name' => 'Retained Earnings', 'account_type' => 'Equity', 'normal_balance' => 'Credit', 'level' => 2, 'is_control_account' => 0],
            
            // Revenue
            ['account_code' => '4000', 'account_name' => 'Sales Revenue', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'level' => 1, 'is_control_account' => 0],
            ['account_code' => '4100', 'account_name' => 'Services Revenue', 'account_type' => 'Revenue', 'normal_balance' => 'Credit', 'level' => 2, 'is_control_account' => 0],
            
            // Expenses
            ['account_code' => '5000', 'account_name' => 'Cost of Goods Sold (COGS)', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'level' => 1, 'is_control_account' => 0],
            ['account_code' => '6000', 'account_name' => 'Operating Expenses', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'level' => 1, 'is_control_account' => 1],
            ['account_code' => '6010', 'account_name' => 'Salaries & Wages Expense', 'account_type' => 'Expense', 'normal_balance' => 'Debit', 'level' => 2, 'is_control_account' => 0],
        ];

        $stmt = $db->prepare("
            INSERT IGNORE INTO chart_of_accounts 
            (company_id, account_code, account_name, account_type, normal_balance, level, is_control_account) 
            VALUES (:cid, :code, :name, :type, :balance, :level, :ctrl)
        ");

        foreach ($accounts as $acc) {
            $stmt->execute([
                ':cid' => $companyId,
                ':code' => $acc['account_code'],
                ':name' => $acc['account_name'],
                ':type' => $acc['account_type'],
                ':balance' => $acc['normal_balance'],
                ':level' => $acc['level'],
                ':ctrl' => $acc['is_control_account']
            ]);
        }
    }
}