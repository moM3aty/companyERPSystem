<?php
// app/models/Lead.php
class Lead extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'leads';
    }

    public function getAllLeads(): array {
        $sql = "SELECT l.*, u.name as assigned_name 
                FROM {$this->table} l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                ORDER BY l.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getLeadById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createLead(array $data): bool {
        $sql = "INSERT INTO {$this->table} (name, company, email, phone, source, status, assigned_to, notes, created_at) 
                VALUES (:name, :company, :email, :phone, :source, :status, :assigned_to, :notes, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':source', $data['source']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':assigned_to', $data['assigned_to'], PDO::PARAM_INT);
        $this->db->bind(':notes', $data['notes']);
        
        return $this->db->execute();
    }

    public function updateLead(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, company = :company, email = :email, phone = :phone, 
                    source = :source, status = :status, assigned_to = :assigned_to, notes = :notes 
                WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':source', $data['source']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':assigned_to', $data['assigned_to'], PDO::PARAM_INT);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

      public function deleteLead(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function updateLeadStatus(int $id, string $status): bool {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        if ($this->db->execute()) {
            // تسجيل الحركة في السجل الآلي
            ActivityLog::logAction('UPDATE', 'Leads', $id, "تم تحديث حالة العميل المحتمل إلى: {$status}");
            return true;
        }
        return false;
    }
}