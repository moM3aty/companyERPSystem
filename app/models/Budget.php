<?php
// app/models/Budget.php

class Budget extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'budgets';
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
            'company_id'      => "INT DEFAULT 1",
            'fiscal_year'     => "VARCHAR(4) NOT NULL",
            'category_id'     => "INT NOT NULL",
            'amount'          => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'notes'           => "TEXT NULL",
            'created_by'      => "INT NOT NULL",
            'created_at'      => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllBudgets($year = null) {
        $year = $year ?: date('Y');
        
        try {
            // جلب الموازنات مع حساب المنصرف الفعلي من جدول expenses
            $sql = "SELECT b.*, ec.name as category_name,
                    (SELECT COALESCE(SUM(total_amount), 0) FROM expenses WHERE category_id = b.category_id AND YEAR(expense_date) = b.fiscal_year) as actual_spent
                    FROM {$this->table} b
                    LEFT JOIN expense_categories ec ON b.category_id = ec.id
                    WHERE b.company_id = :cid AND b.fiscal_year = :yr
                    ORDER BY b.amount DESC";
                    
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':yr', $year);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function createBudget($data) {
        try {
            // التحقق إذا كان هناك موازنة لنفس التصنيف في نفس السنة
            $this->db->query("SELECT id FROM {$this->table} WHERE category_id = :cat AND fiscal_year = :yr AND company_id = :cid");
            $this->db->bind(':cat', $data['category_id']);
            $this->db->bind(':yr', $data['fiscal_year']);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            
            if ($this->db->single()) {
                throw new Exception("يوجد موازنة مسجلة مسبقاً لهذا التصنيف في هذه السنة. يرجى تعديلها أو حذفها بدلاً من إضافة واحدة جديدة.");
            }

            $sql = "INSERT INTO {$this->table} (company_id, fiscal_year, category_id, amount, notes, created_by) 
                    VALUES (:cid, :yr, :cat, :amt, :notes, :user)";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':yr', $data['fiscal_year']);
            $this->db->bind(':cat', $data['category_id']);
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            return $this->db->execute();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteBudget($id) {
        try {
            $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}