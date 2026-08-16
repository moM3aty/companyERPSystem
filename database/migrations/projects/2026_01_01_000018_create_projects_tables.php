<?php
// Path: database/migrations/projects/2026_01_01_000018_create_projects_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateProjectsTables extends Migration
{
    public function up(): void
    {
        // 1. Projects (المشاريع)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS projects (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                code VARCHAR(100) NOT NULL,
                name VARCHAR(255) NOT NULL,
                customer_id BIGINT UNSIGNED NULL,
                manager_id BIGINT UNSIGNED NOT NULL,
                cost_center_id BIGINT UNSIGNED NULL,
                status ENUM('planned', 'active', 'on_hold', 'completed', 'cancelled') DEFAULT 'planned',
                start_date DATE NOT NULL,
                end_date DATE NULL,
                budget DECIMAL(18, 4) DEFAULT 0.0000,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_project_code (company_id, code),
                CONSTRAINT fk_proj_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_proj_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
                CONSTRAINT fk_proj_manager FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Project Tasks (المهام)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS project_tasks (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                project_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                assigned_to BIGINT UNSIGNED NOT NULL,
                status ENUM('todo', 'in_progress', 'review', 'done') DEFAULT 'todo',
                priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
                estimated_hours DECIMAL(10, 2) DEFAULT 0.00,
                logged_hours DECIMAL(10, 2) DEFAULT 0.00,
                start_date DATE NULL,
                due_date DATE NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_task_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                CONSTRAINT fk_task_assignee FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Project Timesheets (سجلات الوقت)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS project_timesheets (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                project_id BIGINT UNSIGNED NOT NULL,
                task_id BIGINT UNSIGNED NOT NULL,
                employee_id BIGINT UNSIGNED NOT NULL,
                log_date DATE NOT NULL,
                hours DECIMAL(10, 2) NOT NULL,
                description VARCHAR(500) NOT NULL,
                status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'submitted',
                approved_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_time_task FOREIGN KEY (task_id) REFERENCES project_tasks(id) ON DELETE CASCADE,
                CONSTRAINT fk_time_employee FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS project_timesheets;");
        $this->connection->statement("DROP TABLE IF EXISTS project_tasks;");
        $this->connection->statement("DROP TABLE IF EXISTS projects;");
    }
}