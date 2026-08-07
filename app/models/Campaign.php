<?php
// المسار: app/models/Campaign.php

class Campaign extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'campaigns';
    }

    public function getAllCampaigns(): array {
        $sql = "SELECT c.*, u.name as created_by_name 
                FROM {$this->table} c 
                LEFT JOIN users u ON c.created_by = u.id 
                ORDER BY c.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getCampaignById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createCampaign(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (name, type, status, start_date, end_date, budget, target_audience, description, created_by, created_at) 
                VALUES 
                (:name, :type, :status, :start_date, :end_date, :budget, :target_audience, :description, :created_by, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':target_audience', $data['target_audience']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':created_by', Session::getUserId(), PDO::PARAM_INT);
        
        if ($this->db->execute()) {
            $campaignId = $this->db->lastInsertId();
            ActivityLog::logAction('CREATE', 'Campaigns', $campaignId, "تم إنشاء حملة تسويقية جديدة: {$data['name']}");
            return true;
        }
        return false;
    }

    public function updateCampaign(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, type = :type, status = :status, start_date = :start_date, 
                    end_date = :end_date, budget = :budget, target_audience = :target_audience, 
                    description = :description 
                WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':target_audience', $data['target_audience']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        if ($this->db->execute()) {
            ActivityLog::logAction('UPDATE', 'Campaigns', $id, "تم تعديل بيانات الحملة التسويقية: {$data['name']}");
            return true;
        }
        return false;
    }

    public function deleteCampaign(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        if ($this->db->execute()) {
            ActivityLog::logAction('DELETE', 'Campaigns', $id, "تم حذف حملة تسويقية من النظام");
            return true;
        }
        return false;
    }
}