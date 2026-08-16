<?php
// Path: database/migrations/administration/2026_01_01_000004_create_roles_permissions_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateRolesPermissionsTables extends Migration
{
    public function up(): void
    {
        // 1. Roles
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS roles (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NULL,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(255) NULL,
                is_system_role TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_roles_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Permissions (Atomic Definitions)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS permissions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                module VARCHAR(50) NOT NULL,
                resource VARCHAR(50) NOT NULL,
                action VARCHAR(50) NOT NULL,
                description VARCHAR(255) NULL,
                UNIQUE KEY unique_permission (module, resource, action)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Role-Permission Mapping
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS role_permissions (
                role_id BIGINT UNSIGNED NOT NULL,
                permission_id BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY (role_id, permission_id),
                CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
                CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. User-Role Mapping
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS user_roles (
                user_id BIGINT UNSIGNED NOT NULL,
                role_id BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY (user_id, role_id),
                CONSTRAINT fk_ur_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS user_roles;");
        $this->connection->statement("DROP TABLE IF EXISTS role_permissions;");
        $this->connection->statement("DROP TABLE IF EXISTS permissions;");
        $this->connection->statement("DROP TABLE IF EXISTS roles;");
    }
}