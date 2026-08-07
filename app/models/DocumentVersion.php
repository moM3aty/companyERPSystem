<?php
// المسار: app/models/DocumentVersion.php

class DocumentVersion extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'document_versions';
    }

    /**
     * جلب سجل الإصدارات لوثيقة معينة
     */
    public function getVersionsByDocument(int $documentId): array {
        $sql = "SELECT dv.*, u.name as uploader_name 
                FROM {$this->table} dv 
                JOIN users u ON dv.uploaded_by = u.id 
                WHERE dv.document_id = :document_id 
                ORDER BY dv.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':document_id', $documentId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * حفظ إصدار جديد
     */
    public function addVersion(array $data): bool {
        $sql = "INSERT INTO {$this->table} (document_id, version_number, file_name, file_path, file_size, uploaded_by, created_at) 
                VALUES (:document_id, :version_number, :file_name, :file_path, :file_size, :uploaded_by, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':document_id', $data['document_id'], PDO::PARAM_INT);
        $this->db->bind(':version_number', $data['version_number']);
        $this->db->bind(':file_name', $data['file_name']);
        $this->db->bind(':file_path', $data['file_path']);
        $this->db->bind(':file_size', $data['file_size'], PDO::PARAM_INT);
        $this->db->bind(':uploaded_by', Session::getUserId(), PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}