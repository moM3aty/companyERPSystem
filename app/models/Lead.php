<?php
// المسار: app/models/Lead.php

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
        $sql = "SELECT l.*, u.name as assigned_name 
                FROM {$this->table} l 
                LEFT JOIN users u ON l.assigned_to = u.id 
                WHERE l.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createLead(array $data): bool {
        $sql = "INSERT INTO {$this->table} (name, company, email, phone, source, status, assigned_to, notes, created_at) 
                VALUES (:name, :company, :email, :phone, :source, 'new', :assigned_to, :notes, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':source', $data['source']);
        $this->db->bind(':assigned_to', $data['assigned_to'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':notes', $data['notes']);
        
        return $this->db->execute();
    }

    public function getFollowUps(int $leadId): array {
        $sql = "SELECT f.*, u.name as created_by_name 
                FROM follow_ups f 
                LEFT JOIN users u ON f.created_by = u.id 
                WHERE f.lead_id = :lead_id 
                ORDER BY f.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':lead_id', $leadId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function addFollowUp(array $data): bool {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO follow_ups (lead_id, date, type, notes, next_action_date, created_by, created_at) 
                    VALUES (:lead_id, :date, :type, :notes, :next_action_date, :created_by, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':lead_id', $data['lead_id'], PDO::PARAM_INT);
            $this->db->bind(':date', $data['date']);
            $this->db->bind(':type', $data['type']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':next_action_date', $data['next_action_date'] ?: null);
            $this->db->bind(':created_by', Session::getUserId(), PDO::PARAM_INT);
            $this->db->execute();

            // Update lead status to 'contacted' if it's currently 'new'
            $this->db->query("UPDATE {$this->table} SET status = 'contacted' WHERE id = :lead_id AND status = 'new'");
            $this->db->bind(':lead_id', $data['lead_id'], PDO::PARAM_INT);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateStatus(int $id, string $status): bool {
        $this->db->query("UPDATE {$this->table} SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}