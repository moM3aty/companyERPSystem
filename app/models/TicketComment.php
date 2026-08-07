<?php
// المسار: app/models/TicketComment.php

class TicketComment extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'ticket_comments';
    }

    /**
     * جلب جميع التعليقات والمرفقات لتذكرة معينة
     */
    public function getCommentsByTicket(int $ticketId): array {
        $sql = "SELECT tc.*, u.name as user_name, u.role as user_role 
                FROM {$this->table} tc 
                JOIN users u ON tc.user_id = u.id 
                WHERE tc.ticket_id = :ticket_id 
                ORDER BY tc.created_at ASC";
        $this->db->query($sql);
        $this->db->bind(':ticket_id', $ticketId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * إضافة تعليق مع مرفق (اختياري)
     */
    public function addComment(array $data): bool {
        $sql = "INSERT INTO {$this->table} (ticket_id, user_id, comment, attachment_path, created_at) 
                VALUES (:ticket_id, :user_id, :comment, :attachment_path, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':ticket_id', $data['ticket_id'], PDO::PARAM_INT);
        $this->db->bind(':user_id', Session::getUserId(), PDO::PARAM_INT);
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':attachment_path', $data['attachment_path'] ?? null);
        
        return $this->db->execute();
    }
}