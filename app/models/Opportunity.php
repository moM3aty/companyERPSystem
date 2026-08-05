<?php
// app/models/Opportunity.php

class Opportunity extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'opportunities';
    }

    /**
     * جلب جميع الفرص مع بيانات العميل والموظف المسؤول
     */
    public function getAllOpportunities(): array {
        $sql = "SELECT o.*, 
                       c.name as customer_name, 
                       u.name as assigned_name 
                FROM {$this->table} o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                LEFT JOIN users u ON o.assigned_to = u.id 
                ORDER BY o.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * إنشاء فرصة بيعية جديدة
     */
    public function createOpportunity(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (customer_id, title, description, stage, estimated_value, probability, expected_close_date, assigned_to, created_at) 
                VALUES 
                (:customer_id, :title, :description, :stage, :estimated_value, :probability, :expected_close_date, :assigned_to, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':customer_id', $data['customer_id'], PDO::PARAM_INT);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':stage', $data['stage']);
        $this->db->bind(':estimated_value', $data['estimated_value']);
        $this->db->bind(':probability', $data['probability'], PDO::PARAM_INT);
        
        // معالجة التواريخ والمسؤول في حال كانت فارغة
        if (empty($data['expected_close_date'])) {
            $this->db->bind(':expected_close_date', null, PDO::PARAM_NULL);
        } else {
            $this->db->bind(':expected_close_date', $data['expected_close_date']);
        }

        if (empty($data['assigned_to'])) {
            $this->db->bind(':assigned_to', null, PDO::PARAM_NULL);
        } else {
            $this->db->bind(':assigned_to', $data['assigned_to'], PDO::PARAM_INT);
        }
        
        return $this->db->execute();
    }

    /**
     * جلب فرصة بيعية واحدة
     */
    public function getOpportunityById(int $id): ?object {
        $sql = "SELECT o.*, c.name as customer_name, u.name as assigned_name 
                FROM {$this->table} o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                LEFT JOIN users u ON o.assigned_to = u.id 
                WHERE o.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
}