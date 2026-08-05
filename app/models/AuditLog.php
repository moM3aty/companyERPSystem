<?php
class AuditLog extends Model {
    protected string $table = 'audit_logs';
    
    /**
     * تسجيل حدث
     */
    public function log($userId, $action, $tableName, $recordId = null, $oldData = null, $newData = null) {
        $this->db->query('
            INSERT INTO audit_logs 
            (user_id, action, table_name, record_id, old_data, new_data, ip_address, user_agent)
            VALUES 
            (:user_id, :action, :table_name, :record_id, :old_data, :new_data, :ip, :ua)
        ');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':action', $action);
        $this->db->bind(':table_name', $tableName);
        $this->db->bind(':record_id', $recordId, PDO::PARAM_INT);
        $this->db->bind(':old_data', $oldData ? json_encode($oldData) : null);
        $this->db->bind(':new_data', $newData ? json_encode($newData) : null);
        $this->db->bind(':ip', $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->bind(':ua', $_SERVER['HTTP_USER_AGENT'] ?? null);
        return $this->db->execute();
    }
    
    /**
     * جلب السجل حسب المستخدم
     */
    public function getByUser($userId, $limit = 50) {
        $this->db->query('SELECT * FROM audit_logs WHERE user_id = :uid ORDER BY id DESC LIMIT :lim');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}