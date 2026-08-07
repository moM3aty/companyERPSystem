<?php
// app/models/Opportunity.php

class Opportunity extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'opportunities';
    }

    public function getAllOpportunities(): array {
        $sql = "SELECT o.*, 
                       c.name as customer_name, 
                       u.name as assigned_name 
                FROM {$this->table} o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                LEFT JOIN users u ON o.assigned_to = u.id 
                ORDER BY o.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getOpportunityById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createOpportunity(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (customer_id, title, description, stage, estimated_value, probability, expected_close_date, assigned_to, created_at) 
                VALUES 
                (:customer_id, :title, :description, :stage, :estimated_value, :probability, :expected_close_date, :assigned_to, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':customer_id', $data['customer_id'], PDO::PARAM_INT);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':stage', $data['stage']);
        $this->db->bind(':estimated_value', $data['estimated_value']);
        $this->db->bind(':probability', $data['probability'], PDO::PARAM_INT);
        $this->db->bind(':expected_close_date', empty($data['expected_close_date']) ? null : $data['expected_close_date']);
        $this->db->bind(':assigned_to', empty($data['assigned_to']) ? null : $data['assigned_to'], PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    // دالة التعديل (Update)
    public function updateOpportunity(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET customer_id = :customer_id, title = :title, description = :description, 
                    stage = :stage, estimated_value = :estimated_value, probability = :probability, 
                    expected_close_date = :expected_close_date, assigned_to = :assigned_to 
                WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':customer_id', $data['customer_id'], PDO::PARAM_INT);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':stage', $data['stage']);
        $this->db->bind(':estimated_value', $data['estimated_value']);
        $this->db->bind(':probability', $data['probability'], PDO::PARAM_INT);
        $this->db->bind(':expected_close_date', empty($data['expected_close_date']) ? null : $data['expected_close_date']);
        $this->db->bind(':assigned_to', empty($data['assigned_to']) ? null : $data['assigned_to'], PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

   public function deleteOpportunity(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function updateStage(int $id, string $stage): bool {
        $sql = "UPDATE {$this->table} SET stage = :stage WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':stage', $stage);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        if ($this->db->execute()) {
            ActivityLog::logAction('UPDATE', 'Opportunities', $id, "تحديث مرحلة الفرصة البيعية إلى: {$stage}");
            return true;
        }
        return false;
    }
}