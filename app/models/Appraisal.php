<?php
// app/models/Appraisal.php

class Appraisal extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_appraisals';
    }

    public function getAllAppraisals(): array {
        $sql = "SELECT a.*, 
                       e.name as employee_name, 
                       e.position,
                       u.name as evaluator_name 
                FROM {$this->table} a 
                JOIN employees e ON a.employee_id = e.id 
                JOIN users u ON a.evaluator_id = u.id 
                ORDER BY a.evaluation_date DESC, a.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createAppraisal(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (employee_id, evaluation_date, performance_score, behavior_score, attendance_score, total_score, grade, evaluator_id, comments, created_at) 
                VALUES 
                (:employee_id, :evaluation_date, :performance_score, :behavior_score, :attendance_score, :total_score, :grade, :evaluator_id, :comments, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':evaluation_date', $data['evaluation_date']);
        $this->db->bind(':performance_score', $data['performance_score'], PDO::PARAM_INT);
        $this->db->bind(':behavior_score', $data['behavior_score'], PDO::PARAM_INT);
        $this->db->bind(':attendance_score', $data['attendance_score'], PDO::PARAM_INT);
        $this->db->bind(':total_score', $data['total_score']);
        $this->db->bind(':grade', $data['grade']);
        $this->db->bind(':evaluator_id', $data['evaluator_id'], PDO::PARAM_INT);
        $this->db->bind(':comments', $data['comments']);
        
        return $this->db->execute();
    }
}