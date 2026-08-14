<?php
// app/models/Ticket.php

class Ticket extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'tickets';
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
            'company_id'  => "INT DEFAULT 1",
            'user_id'     => "INT NOT NULL",
            'subject'     => "VARCHAR(255) NOT NULL",
            'description' => "TEXT NOT NULL",
            'priority'    => "VARCHAR(50) DEFAULT 'medium'",
            'status'      => "VARCHAR(50) DEFAULT 'open'",
            'created_at'  => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllTickets() {
        $sql = "SELECT t.*, u.name as user_name 
                FROM {$this->table} t 
                JOIN users u ON t.user_id = u.id 
                WHERE t.company_id = :cid 
                ORDER BY t.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getMyTickets(int $userId) {
        $sql = "SELECT t.*, u.name as user_name 
                FROM {$this->table} t 
                JOIN users u ON t.user_id = u.id 
                WHERE t.user_id = :uid AND t.company_id = :cid 
                ORDER BY t.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function createTicket(array $data) {
        $sql = "INSERT INTO {$this->table} (company_id, user_id, subject, description, priority, status) 
                VALUES (:cid, :uid, :sub, :desc, :pri, 'open')";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':uid', Session::getUserId());
        $this->db->bind(':sub', $data['subject']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':pri', $data['priority']);
        return $this->db->execute();
    }

    public function updateStatus(int $id, string $status) {
        $this->db->query("UPDATE {$this->table} SET status = :st WHERE id = :id AND company_id = :cid");
        $this->db->bind(':st', $status);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteTicket(int $id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
    public function getTicketById(int $id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    // تعديل بيانات التذكرة
    public function updateTicket(int $id, array $data) {
        $sql = "UPDATE {$this->table} SET subject = :sub, description = :desc, priority = :pri WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':sub', $data['subject']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':pri', $data['priority']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}