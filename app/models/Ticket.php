<?php
// app/models/Ticket.php

class Ticket extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'support_tickets';
    }

    public function getAllTickets(): array {
        $sql = "SELECT t.*, 
                       c.name as customer_name, 
                       u.name as assigned_to_name 
                FROM {$this->table} t 
                LEFT JOIN customers c ON t.customer_id = c.id 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.company_id = :cid
                ORDER BY 
                    FIELD(t.status, 'open', 'in_progress', 'resolved', 'closed'), 
                    FIELD(t.priority, 'urgent', 'high', 'medium', 'low'), 
                    t.created_at DESC";
                
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getTicketById(int $id): ?object {
        $sql = "SELECT t.*, c.name as customer_name, c.phone as customer_phone, u.name as assigned_to_name 
                FROM {$this->table} t 
                LEFT JOIN customers c ON t.customer_id = c.id 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.id = :id AND t.company_id = :cid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getTicketStats(): array {
        $this->db->query("SELECT 
                            COUNT(CASE WHEN status = 'open' THEN 1 END) as open_tickets,
                            COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_tickets,
                            COUNT(CASE WHEN status = 'resolved' OR status = 'closed' THEN 1 END) as closed_tickets,
                            COUNT(CASE WHEN priority = 'urgent' AND status != 'closed' AND status != 'resolved' THEN 1 END) as urgent_tickets
                          FROM {$this->table} WHERE company_id = :cid");
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $result = $this->db->single();
        return $result ? (array)$result : ['open_tickets'=>0, 'in_progress_tickets'=>0, 'closed_tickets'=>0, 'urgent_tickets'=>0];
    }

    public function createTicket(array $data): bool {
        $ticketNumber = 'TKT-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO {$this->table} (company_id, ticket_number, customer_id, subject, description, priority, status, assigned_to, created_at) 
                VALUES (:cid, :ticket_number, :customer_id, :subject, :description, :priority, :status, :assigned_to, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $this->db->bind(':ticket_number', $ticketNumber);
        $this->db->bind(':customer_id', $data['customer_id'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':priority', $data['priority']);
        $this->db->bind(':status', 'open');
        $this->db->bind(':assigned_to', $data['assigned_to'] ?: null, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE {$this->table} SET status = :status";
        if (in_array($status, ['resolved', 'closed'])) {
            $sql .= ", resolved_at = NOW()";
        }
        $sql .= " WHERE id = :id AND company_id = :cid";
        
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->execute();
    }
}