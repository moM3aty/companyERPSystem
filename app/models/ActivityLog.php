<?php
// المسار: app/models/ActivityLog.php

class ActivityLog extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'activity_logs';
    }

    /**
     * جلب جميع السجلات مع بيانات المستخدمين
     */
    public function getAllLogs(int $limit = 500): array {
        $sql = "SELECT a.*, u.name as user_name, u.role as user_role 
                FROM {$this->table} a 
                LEFT JOIN users u ON a.user_id = u.id 
                ORDER BY a.created_at DESC 
                LIMIT :limit";
                
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * دالة ثابتة (Static) لتسجيل أي حركة من أي مكان في النظام بسهولة
     * مثال للاستخدام: ActivityLog::logAction('CREATE', 'Invoices', $invoiceId, 'تم إصدار فاتورة جديدة');
     */
    public static function logAction(string $action, string $module, ?int $recordId = null, string $description = ''): bool {
        $db = Database::getInstance();
        $userId = Session::getUserId(); // إذا لم يكن هناك مستخدم (عملية نظام)، ستكون null
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;

        $sql = "INSERT INTO activity_logs (user_id, action, module, record_id, description, ip_address, created_at) 
                VALUES (:user_id, :action, :module, :record_id, :description, :ip_address, NOW())";
        
        $db->query($sql);
        $db->bind(':user_id', $userId ? (int)$userId : null, PDO::PARAM_INT);
        $db->bind(':action', $action);
        $db->bind(':module', $module);
        $db->bind(':record_id', $recordId, PDO::PARAM_INT);
        $db->bind(':description', $description);
        $db->bind(':ip_address', $ipAddress);
        
        return $db->execute();
    }
}