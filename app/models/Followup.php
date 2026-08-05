<?php
// المسار: app/models/Followup.php

class Followup extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'followups';
    }

    public function getAllFollowups(): array {
        $sql = "SELECT f.*, l.name as lead_name, l.company, u.name as creator_name 
                FROM {$this->table} f 
                JOIN leads l ON f.lead_id = l.id 
                LEFT JOIN users u ON f.created_by = u.id 
                ORDER BY f.scheduled_date ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createFollowup(array $data): bool {
        $sql = "INSERT INTO {$this->table} (lead_id, type, scheduled_date, status, notes, created_by, created_at) 
                VALUES (:lead_id, :type, :scheduled_date, 'pending', :notes, :created_by, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':lead_id', $data['lead_id'], PDO::PARAM_INT);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':scheduled_date', $data['scheduled_date']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':created_by', $data['created_by'], PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function markAsCompleted(int $id): bool {
        $this->db->query("UPDATE {$this->table} SET status = 'completed' WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}