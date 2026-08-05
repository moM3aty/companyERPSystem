<?php
// app/models/AuditLog.php

class AuditLog extends Model {
    
    protected string $table = 'audit_logs';
    
    /**
     * تسجيل حدث في النظام
     * @param int $userId معرّف المستخدم
     * @param string $action نوع الإجراء (insert, update, delete, login, logout)
     * @param string $tableName الجدول الذي تمت عليه العملية
     * @param int|null $recordId معرّف السجل
     * @param mixed $oldData البيانات القديمة (قبل التعديل)
     * @param mixed $newData البيانات الجديدة (بعد التعديل)
     */
    public function log(int $userId, string $action, string $tableName, ?int $recordId = null, $oldData = null, $newData = null): bool {
        $this->db->query('
            INSERT INTO audit_logs 
            (user_id, action, table_name, record_id, old_data, new_data, ip_address, user_agent)
            VALUES 
            (:user_id, :action, :table_name, :record_id, :old_data, :new_data, :ip, :ua)
        ');
        
        $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
        $this->db->bind(':action', $action);
        $this->db->bind(':table_name', $tableName);
        $this->db->bind(':record_id', $recordId, PDO::PARAM_INT);
        
        // تحويل المصفوفات إلى JSON قبل التخزين
        $this->db->bind(':old_data', $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null);
        $this->db->bind(':new_data', $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null);
        
        // تسجيل بيانات الشبكة والجهاز
        $this->db->bind(':ip', $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->bind(':ua', $_SERVER['HTTP_USER_AGENT'] ?? null);
        
        return $this->db->execute();
    }
    
    /**
     * جلب سجل النشاطات لمستخدم معين
     */
    public function getByUser(int $userId, int $limit = 50): array {
        $this->db->query('
            SELECT al.*, u.name as user_name 
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.user_id = :uid 
            ORDER BY al.id DESC 
            LIMIT :lim
        ');
        $this->db->bind(':uid', $userId, PDO::PARAM_INT);
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    /**
     * جلب كافة السجلات (تستخدم في صفحة التدقيق)
     */
    public function getAllLogs(int $limit = 100): array {
        $this->db->query('
            SELECT al.*, u.name as user_name 
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.id DESC 
            LIMIT :lim
        ');
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}