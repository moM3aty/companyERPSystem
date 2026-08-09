<?php
// app/models/Company.php

class Company extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'companies';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'name'              => "VARCHAR(255) NOT NULL",
            'domain'            => "VARCHAR(100) DEFAULT NULL",
            'email'             => "VARCHAR(100) DEFAULT NULL",
            'phone'             => "VARCHAR(50) DEFAULT NULL",
            'subscription_plan' => "VARCHAR(50) DEFAULT 'basic'",
            'subscription_end'  => "DATE DEFAULT NULL",
            'max_users'         => "INT DEFAULT 5",
            'max_branches'      => "INT DEFAULT 1",
            'active_modules'    => "TEXT DEFAULT NULL", 
            'status'            => "VARCHAR(20) DEFAULT 'active'",
            'created_at'        => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllCompanies() {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $this->db->resultSet();
    }

    public function getCompanyById(int $id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getCompanyStats(int $companyId) {
        $stats = [
            'users_count' => 0,
            'branches_count' => 0,
            'total_sales' => 0,
            'employees_count' => 0
        ];

        try {
            $this->db->query("SELECT COUNT(id) as count FROM users WHERE company_id = :cid");
            $this->db->bind(':cid', $companyId);
            $stats['users_count'] = $this->db->single()->count ?? 0;

            $this->db->query("SHOW TABLES LIKE 'warehouses'");
            if (!empty($this->db->resultSet())) {
                $this->db->query("SELECT COUNT(id) as count FROM warehouses WHERE company_id = :cid");
                $this->db->bind(':cid', $companyId);
                $stats['branches_count'] = $this->db->single()->count ?? 0;
            }

            $this->db->query("SHOW TABLES LIKE 'invoices'");
            if (!empty($this->db->resultSet())) {
                $this->db->query("SELECT SUM(total_amount) as total FROM invoices WHERE company_id = :cid");
                $this->db->bind(':cid', $companyId);
                $stats['total_sales'] = $this->db->single()->total ?? 0;
            }

            $this->db->query("SHOW TABLES LIKE 'employees'");
            if (!empty($this->db->resultSet())) {
                $this->db->query("SELECT COUNT(id) as count FROM employees WHERE company_id = :cid");
                $this->db->bind(':cid', $companyId);
                $stats['employees_count'] = $this->db->single()->count ?? 0;
            }
        } catch (Exception $e) {}

        return $stats;
    }

    public function createCompany(array $data) {
        $sql = "INSERT INTO {$this->table} 
                (name, domain, email, phone, subscription_plan, subscription_end, max_users, max_branches, active_modules, status) 
                VALUES (:name, :domain, :email, :phone, :plan, :end_date, :max_users, :max_branches, :modules, :status)";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':domain', $data['domain'] ?? null);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':plan', $data['subscription_plan'] ?? 'basic');
        $this->db->bind(':end_date', $data['subscription_end'] ?? null);
        $this->db->bind(':max_users', $data['max_users'] ?? 5);
        $this->db->bind(':max_branches', $data['max_branches'] ?? 1);
        $this->db->bind(':modules', $data['active_modules'] ?? 'pos,inventory,accounting');
        $this->db->bind(':status', $data['status'] ?? 'active');
        
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function updateCompany(int $id, array $data) {
        $sql = "UPDATE {$this->table} 
                SET name = :name, domain = :domain, email = :email, phone = :phone, 
                    subscription_plan = :plan, subscription_end = :end_date, 
                    max_users = :max_users, max_branches = :max_branches, 
                    active_modules = :modules, status = :status 
                WHERE id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':domain', $data['domain'] ?? null);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':plan', $data['subscription_plan']);
        $this->db->bind(':end_date', $data['subscription_end']);
        $this->db->bind(':max_users', $data['max_users']);
        $this->db->bind(':max_branches', $data['max_branches']);
        $this->db->bind(':modules', $data['active_modules']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    public function updateStatus(int $id, string $status) {
        $this->db->query("UPDATE {$this->table} SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function hardDeleteCompany(int $companyId) {
        if ($companyId == 1) return false;

        try {
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
            $this->db->execute();

            $tablesToDeleteFrom = [
                'users', 'products', 'categories', 'product_batches',
                'customers', 'leads', 'opportunities', 'followups',
                'suppliers', 'purchases', 'purchase_returns', 'purchase_requests',
                'invoices', 'invoice_items', 'quotes', 'sales_orders',
                'warehouses', 'stock_transfers', 'stocktakes',
                'treasuries', 'treasury_transactions', 'sales_collections',
                'chart_of_accounts', 'journal_entries', 'journal_lines', 'expenses',
                'employees', 'employee_contracts', 'attendance', 'payroll',
                'projects', 'project_tasks', 'timesheets', 'contracts',
                'documents', 'tickets', 'activity_logs', 'settings'
            ];

            foreach ($tablesToDeleteFrom as $table) {
                try {
                    $this->db->query("SHOW TABLES LIKE '{$table}'");
                    if (!empty($this->db->resultSet())) {
                        $this->db->query("DELETE FROM `{$table}` WHERE company_id = :cid");
                        $this->db->bind(':cid', $companyId);
                        $this->db->execute();
                    }
                } catch(Exception $e) { continue; }
            }

            $this->db->query("DELETE FROM {$this->table} WHERE id = :cid");
            $this->db->bind(':cid', $companyId);
            $this->db->execute();
            
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
            $this->db->execute();
            
            return true;

        } catch(Exception $e) {
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
            $this->db->execute();
            return false;
        }
    }
}