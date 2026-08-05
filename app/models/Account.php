<?php
// المسار: app/models/Account.php

class Account extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'chart_of_accounts';
    }

    /**
     * جلب جميع الحسابات لترتيب دليل الحسابات
     */
    public function getChartOfAccounts(): array {
        // جلب الحسابات مع اسم الحساب الأب (إن وجد)
        $sql = "SELECT a.*, p.name as parent_name 
                FROM {$this->table} a 
                LEFT JOIN {$this->table} p ON a.parent_id = p.id 
                ORDER BY a.type, a.code ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * جلب الحسابات الرئيسية (للاستخدام كآباء)
     */
    public function getParentAccounts(): array {
        $sql = "SELECT id, code, name, type FROM {$this->table} ORDER BY code ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * التحقق من تكرار كود الحساب
     */
    public function codeExists(string $code): bool {
        $sql = "SELECT id FROM {$this->table} WHERE code = :code";
        $this->db->query($sql);
        $this->db->bind(':code', $code);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * إضافة حساب جديد
     */
    public function createAccount(array $data): bool {
        $sql = "INSERT INTO {$this->table} (code, name, type, parent_id, balance, is_active, created_at) 
                VALUES (:code, :name, :type, :parent_id, :balance, 1, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':parent_id', $data['parent_id'], PDO::PARAM_INT);
        $this->db->bind(':balance', $data['balance']);
        
        return $this->db->execute();
    }
}