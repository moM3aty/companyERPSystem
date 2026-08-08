<?php
// app/models/Employee.php

class Employee extends Model {
    
      public function __construct() {
        parent::__construct();
        $this->table = 'employees';
        
        // 🟢 استدعاء دالة الترقية التلقائية لتفادي أخطاء نقص الأعمدة
        $this->autoUpgradeTable();
    }

    /**
     * دالة ذكية لفحص وإنشاء الأعمدة المفقودة في جدول الموظفين
     */
    private function autoUpgradeTable() {
        // قائمة بالأعمدة التي قد تكون مفقودة
        $columnsToAdd = [
            'company_id'    => "INT DEFAULT 1",
            'department_id' => "INT NULL",
            'position'      => "VARCHAR(100) NULL",
            'join_date'     => "DATE NULL",
            'salary'        => "DECIMAL(10,2) DEFAULT 0.00",
            'status'        => "VARCHAR(50) DEFAULT 'active'"
        ];

        foreach ($columnsToAdd as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '{$colName}'");
                $exists = $this->db->resultSet();
                
                if (empty($exists)) {
                    $this->db->query("ALTER TABLE {$this->table} ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {
                // تجاهل بصمت حتى لا يتوقف النظام
            }
        }
    }

    public function getAllEmployees(): array {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY created_at DESC");
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function count(): int {
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE company_id = :cid");
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return (int)($this->db->single()->total ?? 0);
    }

    public function getEmployeeById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createEmployee(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, email, phone, position, salary, join_date, status, created_at) 
                VALUES (:cid, :name, :email, :phone, :position, :salary, :join_date, :status, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':position', $data['position']);
        $this->db->bind(':salary', $data['salary']);
        $this->db->bind(':join_date', $data['join_date']);
        $this->db->bind(':status', $data['status'] ?? 'active');
        
        return $this->db->execute();
    }

    public function updateEmployee(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, email = :email, phone = :phone, position = :position, salary = :salary, join_date = :join_date, status = :status 
                WHERE id = :id AND company_id = :cid";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':position', $data['position']);
        $this->db->bind(':salary', $data['salary']);
        $this->db->bind(':join_date', $data['join_date']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        
        return $this->db->execute();
    }
  public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM employees WHERE email = :email AND company_id = :cid";
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        
        if ($excludeId) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
    public function deleteEmployee(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->execute();
    }
    
}