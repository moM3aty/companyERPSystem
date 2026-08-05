<?php
// المسار: app/models/Document.php

class Document extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'documents';
    }

    /**
     * جلب جميع الوثائق مع بيانات الرافع
     * يمكن تخصيصها لاحقاً لجلب الوثائق العامة أو الخاصة بالمستخدم فقط
     */
    public function getAllDocuments(int $userId, string $role): array {
        // إذا كان مديراً يرى كل شيء، وإلا يرى العام والخاص به
        if ($role === 'admin') {
            $sql = "SELECT d.*, u.name as uploaded_by_name 
                    FROM {$this->table} d 
                    LEFT JOIN users u ON d.uploaded_by = u.id 
                    ORDER BY d.created_at DESC";
            $this->db->query($sql);
        } else {
            $sql = "SELECT d.*, u.name as uploaded_by_name 
                    FROM {$this->table} d 
                    LEFT JOIN users u ON d.uploaded_by = u.id 
                    WHERE d.access_level = 'public' OR d.uploaded_by = :user_id 
                    ORDER BY d.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
        }
        
        return $this->db->resultSet();
    }

    /**
     * حفظ بيانات الوثيقة في قاعدة البيانات بعد رفع الملف
     */
    public function saveDocumentInfo(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (title, file_name, file_type, file_size, folder_path, uploaded_by, access_level, created_at) 
                VALUES 
                (:title, :file_name, :file_type, :file_size, :folder_path, :uploaded_by, :access_level, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':file_name', $data['file_name']);
        $this->db->bind(':file_type', $data['file_type']);
        $this->db->bind(':file_size', $data['file_size'], PDO::PARAM_INT);
        $this->db->bind(':folder_path', $data['folder_path']);
        $this->db->bind(':uploaded_by', $data['uploaded_by'], PDO::PARAM_INT);
        $this->db->bind(':access_level', $data['access_level']);
        
        return $this->db->execute();
    }

    /**
     * جلب تفاصيل وثيقة واحدة لمعرفة مسارها عند التنزيل أو الحذف
     */
    public function getDocumentById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
}