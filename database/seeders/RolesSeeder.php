<?php
// Path: database/seeders/RolesSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Database\Seeder;

/**
 * Enterprise Roles Seeder
 * Injects default system-wide roles (like Super Admin).
 */
class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roleName = 'Super Admin';
        
        $exists = $this->db->connection()->selectOne("SELECT id FROM roles WHERE name = ? AND is_system_role = 1", [$roleName]);

        if (!$exists) {
            $this->db->connection()->insert(
                "INSERT INTO roles (company_id, name, description, is_system_role, created_at) VALUES (NULL, ?, 'Master system administrator with all access.', 1, ?)",
                [$roleName, date('Y-m-d H:i:s')]
            );
            
            $roleId = $this->db->connection()->lastInsertId();
            
            // Assign all existing permissions to Super Admin
            $permissions = $this->db->connection()->select("SELECT id FROM permissions");
            foreach ($permissions as $perm) {
                $this->db->connection()->insert(
                    "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                    [$roleId, $perm['id']]
                );
            }
        }
    }
}