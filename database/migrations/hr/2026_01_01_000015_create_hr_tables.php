<?php
// Path: database/migrations/hr/2026_01_01_000015_create_hr_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateHrTables extends Migration
{
    public function up(): void
    {
        // 1. Organization Nodes (Divisions, Departments, Teams)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS organization_nodes (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                parent_id BIGINT UNSIGNED NULL,
                manager_id BIGINT UNSIGNED NULL,
                cost_center_id BIGINT UNSIGNED NULL,
                name VARCHAR(150) NOT NULL,
                type ENUM('division', 'department', 'team') NOT NULL,
                level INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_org_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_org_parent FOREIGN KEY (parent_id) REFERENCES organization_nodes(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Employees
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS hr_employees (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                employee_code VARCHAR(50) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                national_id VARCHAR(50) NOT NULL,
                department_id BIGINT UNSIGNED NULL,
                manager_id BIGINT UNSIGNED NULL,
                hire_date DATE NOT NULL,
                status ENUM('active', 'on_leave', 'terminated', 'suspended') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_emp_code (company_id, employee_code),
                UNIQUE KEY unique_emp_email (company_id, email),
                UNIQUE KEY unique_emp_nat_id (company_id, national_id),
                CONSTRAINT fk_emp_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_emp_dept FOREIGN KEY (department_id) REFERENCES organization_nodes(id) ON DELETE SET NULL,
                CONSTRAINT fk_emp_manager FOREIGN KEY (manager_id) REFERENCES hr_employees(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Contracts
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS hr_contracts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                employee_id BIGINT UNSIGNED NOT NULL,
                contract_type ENUM('full_time', 'part_time', 'freelance') NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NULL,
                basic_salary DECIMAL(18, 4) NOT NULL,
                currency_id BIGINT UNSIGNED NOT NULL,
                working_hours INT DEFAULT 8,
                probation_days INT DEFAULT 0,
                status ENUM('draft', 'active', 'expired', 'terminated') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                CONSTRAINT fk_cont_employee FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Attendance
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS hr_attendance_records (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                employee_id BIGINT UNSIGNED NOT NULL,
                record_date DATE NOT NULL,
                check_in_time TIME NULL,
                check_out_time TIME NULL,
                status ENUM('present', 'absent', 'late', 'on_leave') DEFAULT 'present',
                late_minutes INT DEFAULT 0,
                notes VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_att_emp_date (company_id, employee_id, record_date),
                CONSTRAINT fk_att_employee FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 5. Leave Requests
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS hr_leave_requests (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                employee_id BIGINT UNSIGNED NOT NULL,
                leave_type ENUM('annual', 'sick', 'unpaid', 'maternity') NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                total_days INT NOT NULL,
                status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
                reason VARCHAR(500) NULL,
                approved_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_leave_employee FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS hr_leave_requests;");
        $this->connection->statement("DROP TABLE IF EXISTS hr_attendance_records;");
        $this->connection->statement("DROP TABLE IF EXISTS hr_contracts;");
        $this->connection->statement("DROP TABLE IF EXISTS hr_employees;");
        $this->connection->statement("DROP TABLE IF EXISTS organization_nodes;");
    }
}