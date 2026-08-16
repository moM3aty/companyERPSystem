<?php
// Path: database/migrations/administration/2026_01_01_000008_create_security_tracking_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateSecurityTrackingTables extends Migration
{
    public function up(): void
    {
        // 1. Login History (SIEM Tracking)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS login_history (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                email VARCHAR(150) NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT NULL,
                is_success TINYINT(1) DEFAULT 0,
                attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_login_history_ip (ip_address),
                INDEX idx_login_history_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. User Devices (Device Trust)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS security_user_devices (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                device_id VARCHAR(150) NOT NULL,
                device_name VARCHAR(255) NULL,
                ip_address VARCHAR(45) NULL,
                is_trusted TINYINT(1) DEFAULT 0,
                revoked_at TIMESTAMP NULL DEFAULT NULL,
                last_active_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_device_user (user_id, device_id),
                CONSTRAINT fk_sud_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Password Histories (Prevent Reuse)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS password_histories (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_ph_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. User OTPs (2FA / MFA)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS user_otps (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                otp_code VARCHAR(10) NOT NULL,
                is_used TINYINT(1) DEFAULT 0,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_otp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS user_otps;");
        $this->connection->statement("DROP TABLE IF EXISTS password_histories;");
        $this->connection->statement("DROP TABLE IF EXISTS security_user_devices;");
        $this->connection->statement("DROP TABLE IF EXISTS login_history;");
    }
}