<?php
class Notification extends Model {
    protected string $table = 'notifications';
    
    /**
     * إرسال إشعار لمستخدم
     */
    public function send($userId, $type, $title, $message, $link = null) {
        $this->db->query('
            INSERT INTO notifications (user_id, type, title, message, link)
            VALUES (:user, :type, :title, :msg, :link)
        ');
        $this->db->bind(':user', $userId, PDO::PARAM_INT);
        $this->db->bind(':type', $type);
        $this->db->bind(':title', $title);
        $this->db->bind(':msg', $message);
        $this->db->bind(':link', $link);
        return $this->db->execute();
    }
    
    /**
     * جلب الإشعارات غير المقروءة
     */
    public function getUnread($userId, $limit = 20) {
        $this->db->query('
            SELECT * FROM notifications 
            WHERE user_id = :user AND is_read = 0
            ORDER BY id DESC LIMIT :lim
        ');
        $this->db->bind(':user', $userId, PDO::PARAM_INT);
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    /**
     * تعيين الإشعار كمقروء
     */
    public function markAsRead($id) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}