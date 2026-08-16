<?php
// Path: database/migrations/core/2026_01_01_000022_create_workflow_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateWorkflowTables extends Migration
{
    public function up(): void
    {
        // 1. Workflow Definitions
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS workflow_definitions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                code VARCHAR(100) NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_wd_code (company_id, code),
                CONSTRAINT fk_wd_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Workflow Versions
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS workflow_versions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                workflow_definition_id BIGINT UNSIGNED NOT NULL,
                version_number INT NOT NULL,
                status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
                published_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_wv_def FOREIGN KEY (workflow_definition_id) REFERENCES workflow_definitions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Workflow Steps
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS workflow_steps (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                workflow_version_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                type ENUM('start', 'process', 'approval', 'end') NOT NULL,
                is_start_step TINYINT(1) DEFAULT 0,
                CONSTRAINT fk_ws_version FOREIGN KEY (workflow_version_id) REFERENCES workflow_versions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Workflow Transitions
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS workflow_transitions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                workflow_version_id BIGINT UNSIGNED NOT NULL,
                from_step_id BIGINT UNSIGNED NOT NULL,
                to_step_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(100) NOT NULL,
                CONSTRAINT fk_wt_version FOREIGN KEY (workflow_version_id) REFERENCES workflow_versions(id) ON DELETE CASCADE,
                CONSTRAINT fk_wt_from FOREIGN KEY (from_step_id) REFERENCES workflow_steps(id) ON DELETE CASCADE,
                CONSTRAINT fk_wt_to FOREIGN KEY (to_step_id) REFERENCES workflow_steps(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 5. Workflow Conditions
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS workflow_conditions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transition_id BIGINT UNSIGNED NOT NULL,
                field_key VARCHAR(100) NOT NULL,
                operator VARCHAR(20) NOT NULL,
                expected_value VARCHAR(255) NOT NULL,
                CONSTRAINT fk_wc_trans FOREIGN KEY (transition_id) REFERENCES workflow_transitions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 6. Workflow Instances
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS workflow_instances (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                workflow_version_id BIGINT UNSIGNED NOT NULL,
                entity_type VARCHAR(100) NOT NULL,
                entity_id BIGINT UNSIGNED NOT NULL,
                current_step_id BIGINT UNSIGNED NOT NULL,
                status ENUM('active', 'completed', 'cancelled', 'failed') DEFAULT 'active',
                payload JSON NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_wi_version FOREIGN KEY (workflow_version_id) REFERENCES workflow_versions(id) ON DELETE CASCADE,
                CONSTRAINT fk_wi_step FOREIGN KEY (current_step_id) REFERENCES workflow_steps(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 7. Standard Approval Requests (Simplified layer overlaying workflow for documents)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS approval_requests (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                document_type VARCHAR(100) NOT NULL,
                document_id BIGINT UNSIGNED NOT NULL,
                requester_id BIGINT UNSIGNED NOT NULL,
                status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 8. Approval Steps & Histories
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS approval_steps (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                approval_request_id BIGINT UNSIGNED NOT NULL,
                approver_id BIGINT UNSIGNED NULL,
                role_id BIGINT UNSIGNED NULL,
                level INT NOT NULL,
                status ENUM('pending', 'approved', 'rejected', 'skipped', 'escalated') DEFAULT 'pending',
                is_current TINYINT(1) DEFAULT 0,
                sla_hours INT DEFAULT 0,
                sla_deadline_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_as_req FOREIGN KEY (approval_request_id) REFERENCES approval_requests(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS approval_histories (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                approval_request_id BIGINT UNSIGNED NOT NULL,
                step_id BIGINT UNSIGNED NOT NULL,
                approver_id BIGINT UNSIGNED NOT NULL,
                action VARCHAR(50) NOT NULL,
                comments TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_ah_req FOREIGN KEY (approval_request_id) REFERENCES approval_requests(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS approval_delegations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                delegator_user_id BIGINT UNSIGNED NOT NULL,
                delegate_user_id BIGINT UNSIGNED NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS approval_delegations;");
        $this->connection->statement("DROP TABLE IF EXISTS approval_histories;");
        $this->connection->statement("DROP TABLE IF EXISTS approval_steps;");
        $this->connection->statement("DROP TABLE IF EXISTS approval_requests;");
        $this->connection->statement("DROP TABLE IF EXISTS workflow_instances;");
        $this->connection->statement("DROP TABLE IF EXISTS workflow_conditions;");
        $this->connection->statement("DROP TABLE IF EXISTS workflow_transitions;");
        $this->connection->statement("DROP TABLE IF EXISTS workflow_steps;");
        $this->connection->statement("DROP TABLE IF EXISTS workflow_versions;");
        $this->connection->statement("DROP TABLE IF EXISTS workflow_definitions;");
    }
}