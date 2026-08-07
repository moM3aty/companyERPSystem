<?php
// app/models/Company.php

class Company extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'companies';
    }

    public function getAllCompanies(): array {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(id) FROM users WHERE company_id = c.id) as users_count 
                FROM {$this->table} c 
                ORDER BY c.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getCompanyById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createCompanyWithAdmin(array $companyData, array $adminData): bool {
        try {
            $this->db->beginTransaction();

            // 1. إنشاء الشركة
            $sqlCompany = "INSERT INTO {$this->table} (name, domain, email, phone, status, subscription_ends_at, created_at) 
                           VALUES (:name, :domain, :email, :phone, :status, :sub_ends, NOW())";
            $this->db->query($sqlCompany);
            $this->db->bind(':name', $companyData['name']);
            $this->db->bind(':domain', $companyData['domain'] ?? null);
            $this->db->bind(':email', $companyData['email'] ?? null);
            $this->db->bind(':phone', $companyData['phone'] ?? null);
            $this->db->bind(':status', $companyData['status'] ?? 'active');
            $this->db->bind(':sub_ends', $companyData['subscription_ends_at'] ?? null);
            $this->db->execute();

            $companyId = $this->db->lastInsertId();

            // 2. إنشاء المدير الأول للشركة
            $sqlAdmin = "INSERT INTO users (company_id, name, email, password, role) 
                         VALUES (:cid, :aname, :aemail, :apass, 'admin')";
            $this->db->query($sqlAdmin);
            $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
            $this->db->bind(':aname', $adminData['name']);
            $this->db->bind(':aemail', $adminData['email']);
            $this->db->bind(':apass', password_hash($adminData['password'], PASSWORD_BCRYPT));
            $this->db->execute();

            ActivityLog::logAction('CREATE', 'Companies', $companyId, "تم تسجيل شركة جديدة بنظام SaaS: {$companyData['name']}");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateCompany(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, domain = :domain, email = :email, phone = :phone, 
                    status = :status, subscription_ends_at = :sub_ends 
                WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':domain', $data['domain'] ?? null);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':sub_ends', $data['subscription_ends_at'] ?? null);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        if($this->db->execute()) {
            ActivityLog::logAction('UPDATE', 'Companies', $id, "تحديث بيانات الشركة: {$data['name']}");
            return true;
        }
        return false;
    }

    public function toggleStatus(int $id, string $status): bool {
        $this->db->query("UPDATE {$this->table} SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}