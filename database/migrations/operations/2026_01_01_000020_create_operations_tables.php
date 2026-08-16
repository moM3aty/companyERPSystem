<?php
// Path: database/migrations/operations/2026_01_01_000020_create_operations_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateOperationsTables extends Migration
{
    public function up(): void
    {
        // ==========================================
        // MAINTENANCE MODULE
        // ==========================================
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS maintenance_plans (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                asset_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                frequency_days INT NOT NULL,
                next_due_date DATE NOT NULL,
                status ENUM('active', 'paused') DEFAULT 'active',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_mp_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_mp_asset FOREIGN KEY (asset_id) REFERENCES fixed_assets(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS maintenance_work_orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                work_order_number VARCHAR(100) NOT NULL,
                maintenance_plan_id BIGINT UNSIGNED NULL,
                asset_id BIGINT UNSIGNED NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NULL,
                assigned_to BIGINT UNSIGNED NULL,
                priority ENUM('low', 'normal', 'high', 'critical') DEFAULT 'normal',
                status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
                scheduled_date DATE NOT NULL,
                completed_at TIMESTAMP NULL DEFAULT NULL,
                estimated_cost DECIMAL(18, 4) DEFAULT 0.0000,
                actual_cost DECIMAL(18, 4) DEFAULT 0.0000,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_wo_no (company_id, work_order_number),
                CONSTRAINT fk_wo_asset FOREIGN KEY (asset_id) REFERENCES fixed_assets(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // ==========================================
        // FLEET MODULE
        // ==========================================
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS fleet_vehicles (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                plate_number VARCHAR(50) NOT NULL,
                make VARCHAR(100) NOT NULL,
                model VARCHAR(100) NOT NULL,
                year INT NOT NULL,
                chassis_number VARCHAR(100) NULL,
                driver_id BIGINT UNSIGNED NULL,
                asset_id BIGINT UNSIGNED NULL,
                status ENUM('active', 'maintenance', 'out_of_service') DEFAULT 'active',
                current_mileage DECIMAL(18, 2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_vehicle_plate (company_id, plate_number),
                CONSTRAINT fk_veh_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_veh_driver FOREIGN KEY (driver_id) REFERENCES hr_employees(id) ON DELETE SET NULL,
                CONSTRAINT fk_veh_asset FOREIGN KEY (asset_id) REFERENCES fixed_assets(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS fleet_trips (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                vehicle_id BIGINT UNSIGNED NOT NULL,
                driver_id BIGINT UNSIGNED NOT NULL,
                start_location VARCHAR(255) NOT NULL,
                end_location VARCHAR(255) NOT NULL,
                start_time TIMESTAMP NOT NULL,
                end_time TIMESTAMP NULL DEFAULT NULL,
                distance_covered DECIMAL(18, 2) DEFAULT 0.00,
                fuel_consumed DECIMAL(18, 2) DEFAULT 0.00,
                trip_cost DECIMAL(18, 4) DEFAULT 0.0000,
                status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_trip_vehicle FOREIGN KEY (vehicle_id) REFERENCES fleet_vehicles(id) ON DELETE CASCADE,
                CONSTRAINT fk_trip_driver FOREIGN KEY (driver_id) REFERENCES hr_employees(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS fleet_trips;");
        $this->connection->statement("DROP TABLE IF EXISTS fleet_vehicles;");
        $this->connection->statement("DROP TABLE IF EXISTS maintenance_work_orders;");
        $this->connection->statement("DROP TABLE IF EXISTS maintenance_plans;");
    }
}