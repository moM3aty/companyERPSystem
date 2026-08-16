<?php
// Path: database/seeders/PermissionsSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Database\Seeder;

/**
 * Enterprise Permissions Seeder
 * Injects the core atomic permissions into the system.
 */
class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Administration
            ['module' => 'administration', 'resource' => 'users', 'action' => 'view', 'description' => 'View Users'],
            ['module' => 'administration', 'resource' => 'users', 'action' => 'create', 'description' => 'Create Users'],
            ['module' => 'administration', 'resource' => 'roles', 'action' => 'view', 'description' => 'View Roles'],
            
            // Accounting
            ['module' => 'accounting', 'resource' => 'journal_entries', 'action' => 'view', 'description' => 'View JVs'],
            ['module' => 'accounting', 'resource' => 'journal_entries', 'action' => 'create', 'description' => 'Create JVs'],
            
            // Sales
            ['module' => 'sales', 'resource' => 'invoices', 'action' => 'create', 'description' => 'Create Sales Invoices'],
        ];

        foreach ($permissions as $perm) {
            $exists = $this->db->connection()->selectOne(
                "SELECT id FROM permissions WHERE module = ? AND resource = ? AND action = ?",
                [$perm['module'], $perm['resource'], $perm['action']]
            );

            if (!$exists) {
                $this->db->connection()->insert(
                    "INSERT INTO permissions (module, resource, action, description) VALUES (?, ?, ?, ?)",
                    [$perm['module'], $perm['resource'], $perm['action'], $perm['description']]
                );
            }
        }
    }
}