<?php
// app/models/Training.php

class Training extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_trainings';
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
            'company_id'      => "INT DEFAULT 1",
            'employee_id'     => "INT NOT NULL",
            'course_name'     => "VARCHAR(255) NOT NULL",
            'provider'        => "VARCHAR(255) NULL",
            'course_date'     => "DATE NOT NULL",
            'expiry_date'     => "DATE NULL",
            'cost'            => "DECIMAL(10,2) DEFAULT 0.00",
            'skills_acquired' => "TEXT NULL",
            'evaluation'      => "VARCHAR(50) DEFAULT 'pending'", // pending, excellent, good, poor
            'certificate_path'=> "VARCHAR(255) NULL",
            'created_at'      => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllTrainings() {
        $sql = "SELECT t.*, e.name as employee_name, e.employee_number 
                FROM {$this->table} t 
                JOIN employees e ON t.employee_id = e.id 
                WHERE t.company_id = :cid 
                ORDER BY t.course_date DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getTrainingById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createTraining($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, course_name, provider, course_date, expiry_date, cost, skills_acquired, evaluation) 
                VALUES (:cid, :emp, :course, :provider, :cdate, :edate, :cost, :skills, :eval)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':course', $data['course_name']);
        $this->db->bind(':provider', $data['provider']);
        $this->db->bind(':cdate', $data['course_date']);
        $this->db->bind(':edate', !empty($data['expiry_date']) ? $data['expiry_date'] : null);
        $this->db->bind(':cost', $data['cost']);
        $this->db->bind(':skills', $data['skills_acquired']);
        $this->db->bind(':eval', $data['evaluation']);
        return $this->db->execute();
    }

    public function updateTraining($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET employee_id = :emp, course_name = :course, provider = :provider, 
                    course_date = :cdate, expiry_date = :edate, cost = :cost, 
                    skills_acquired = :skills, evaluation = :eval 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':course', $data['course_name']);
        $this->db->bind(':provider', $data['provider']);
        $this->db->bind(':cdate', $data['course_date']);
        $this->db->bind(':edate', !empty($data['expiry_date']) ? $data['expiry_date'] : null);
        $this->db->bind(':cost', $data['cost']);
        $this->db->bind(':skills', $data['skills_acquired']);
        $this->db->bind(':eval', $data['evaluation']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteTraining($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}