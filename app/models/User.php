<?php
// app/models/User.php

class User extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'users';
    }

    public function login(string $email, string $password): ?object {
        // جلب المستخدم مع اسم الشركة المرتبطة به (Multi-Tenancy)
        $sql = "SELECT u.*, c.name as company_name, c.status as company_status 
                FROM {$this->table} u 
                LEFT JOIN companies c ON u.company_id = c.id 
                WHERE u.email = :email";
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        $user = $this->db->single();

        if ($user) {
            // التحقق من أن الشركة غير موقوفة (Suspended)
            if ($user->role !== 'super_admin' && $user->company_status === 'suspended') {
                return null; // الشركة موقوفة، لا يمكن الدخول
            }

            if (password_verify($password, $user->password)) {
                return $user;
            }
        }
        return null;
    }

    // جلب موظفي الشركة فقط، أو كل المستخدمين في حالة كان المالك
    public function getUsersByCompany(?int $companyId = null): array {
        if ($companyId) {
            $this->db->query("SELECT u.id, u.name, u.email, u.role, c.name as company_name FROM {$this->table} u LEFT JOIN companies c ON u.company_id = c.id WHERE u.company_id = :cid ORDER BY u.name ASC");
            $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
        } else {
            $this->db->query("SELECT u.id, u.name, u.email, u.role, c.name as company_name FROM {$this->table} u LEFT JOIN companies c ON u.company_id = c.id ORDER BY c.name ASC, u.name ASC");
        }
        return $this->db->resultSet();
    }
    
    public function getUserById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
}