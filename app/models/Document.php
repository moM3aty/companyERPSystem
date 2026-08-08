<?php
// المسار: app/models/Document.php

class Document extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'documents';
        
        // 🟢 الحل السحري لترقية قاعدة البيانات تلقائياً 🟢
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // 1. إنشاء الجدول إن لم يكن موجوداً
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        // 2. فحص وإضافة الأعمدة المفقودة
        $columnsToAdd = [
            'company_id'   => "INT DEFAULT 1",
            'title'        => "VARCHAR(255) NOT NULL",
            'file_name'    => "VARCHAR(255) NOT NULL",
            'file_path'    => "VARCHAR(255) NOT NULL DEFAULT '/uploads/documents/'",
            'file_size'    => "INT NOT NULL DEFAULT 0",
            'file_type'    => "VARCHAR(50) NOT NULL DEFAULT 'unknown'",
            'access_level' => "VARCHAR(50) DEFAULT 'private'",
            'uploaded_by'  => "INT NOT NULL DEFAULT 0",
            'created_at'   => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columnsToAdd as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {
                // تجاهل الأخطاء بصمت
            }
        }
    }

    public function getAllDocuments($userId = null, $userRole = null): array {
        $sql = "SELECT d.*, u.name as uploader_name 
                FROM {$this->table} d 
                LEFT JOIN users u ON d.uploaded_by = u.id 
                WHERE d.company_id = :cid";
                
        // إذا كان المستخدم ليس مديراً، يتم إخفاء الملفات الخاصة بالمستخدمين الآخرين
        if ($userRole !== 'admin') {
            $sql .= " AND (d.access_level = 'public' OR d.uploaded_by = :user_id)";
        }
        
        $sql .= " ORDER BY d.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1, PDO::PARAM_INT);
        if ($userRole !== 'admin') {
            $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
        }
        
        return $this->db->resultSet();
    }

    public function getDocumentById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id') ?: 1, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createDocument(array $data): bool {
        // تم إضافة access_level للحفظ في قاعدة البيانات
        $sql = "INSERT INTO {$this->table} (company_id, title, file_name, file_path, file_size, file_type, access_level, uploaded_by, created_at) 
                VALUES (:cid, :title, :file_name, :file_path, :file_size, :file_type, :access_level, :uploaded_by, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1, PDO::PARAM_INT);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':file_name', $data['file_name']);
        $this->db->bind(':file_path', $data['file_path']);
        $this->db->bind(':file_size', $data['file_size'], PDO::PARAM_INT);
        $this->db->bind(':file_type', $data['file_type']);
        $this->db->bind(':access_level', $data['access_level'] ?? 'private');
        $this->db->bind(':uploaded_by', Session::getUserId(), PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function deleteDocument(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id') ?: 1, PDO::PARAM_INT);
        return $this->db->execute();
    }
}