<?php
// app/models/Appraisal.php

class Appraisal extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_appraisals';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'        => "INT DEFAULT 1",
            'employee_id'       => "INT NOT NULL",
            'evaluation_date'   => "DATE NOT NULL",
            'performance_score' => "INT DEFAULT 0",
            'behavior_score'    => "INT DEFAULT 0",
            'attendance_score'  => "INT DEFAULT 0",
            'total_score'       => "DECIMAL(5,2) DEFAULT 0.00",
            'grade'             => "VARCHAR(50) DEFAULT NULL", // Excellent, Good, Poor
            'evaluator_id'      => "INT NOT NULL",
            'comments'          => "TEXT DEFAULT NULL",
            'created_at'        => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllAppraisals() {
        $sql = "SELECT a.*, e.name as employee_name, u.name as evaluator_name 
                FROM {$this->table} a 
                JOIN employees e ON a.employee_id = e.id 
                LEFT JOIN users u ON a.evaluator_id = u.id 
                WHERE a.company_id = :cid 
                ORDER BY a.evaluation_date DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function createAppraisal($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, evaluation_date, performance_score, behavior_score, attendance_score, total_score, grade, evaluator_id, comments) 
                VALUES (:cid, :emp, :date, :perf, :behav, :att, :total, :grade, :evaluator, :comments)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':date', $data['evaluation_date']);
        $this->db->bind(':perf', $data['performance_score']);
        $this->db->bind(':behav', $data['behavior_score']);
        $this->db->bind(':att', $data['attendance_score']);
        $this->db->bind(':total', $data['total_score']);
        $this->db->bind(':grade', $data['grade']);
        $this->db->bind(':evaluator', Session::getUserId());
        $this->db->bind(':comments', $data['comments']);
        return $this->db->execute();
    }
    
    public function deleteAppraisal($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}