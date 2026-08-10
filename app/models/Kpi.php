<?php
// app/models/Kpi.php

class Kpi extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'hr_kpis';
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
            'company_id'          => "INT DEFAULT 1",
            'employee_id'         => "INT NOT NULL",
            'review_period'       => "VARCHAR(50) NOT NULL", // Q1, Annual, Mid-year
            'kpi_name'            => "VARCHAR(255) NOT NULL",
            'target_value'        => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'actual_value'        => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'achievement_percent' => "DECIMAL(5,2) NOT NULL DEFAULT 0.00",
            'weight'              => "DECIMAL(5,2) NOT NULL DEFAULT 0.00", // Weight %
            'manager_evaluation'  => "TEXT NULL",
            'employee_comments'   => "TEXT NULL",
            'development_plan'    => "TEXT NULL",
            'overall_rating'      => "VARCHAR(50) DEFAULT 'Good'", // Excellent, Good, Needs Improvement
            'created_at'          => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllKpis() {
        $sql = "SELECT k.*, e.name as employee_name, e.position 
                FROM {$this->table} k 
                JOIN employees e ON k.employee_id = e.id 
                WHERE k.company_id = :cid 
                ORDER BY k.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getKpiById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createKpi($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, review_period, kpi_name, target_value, actual_value, achievement_percent, weight, manager_evaluation, employee_comments, development_plan, overall_rating) 
                VALUES (:cid, :emp, :period, :kpi, :target, :actual, :achieve, :weight, :meval, :ecomp, :plan, :rating)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':period', $data['review_period']);
        $this->db->bind(':kpi', $data['kpi_name']);
        $this->db->bind(':target', $data['target_value']);
        $this->db->bind(':actual', $data['actual_value']);
        $this->db->bind(':achieve', $data['achievement_percent']);
        $this->db->bind(':weight', $data['weight']);
        $this->db->bind(':meval', $data['manager_evaluation']);
        $this->db->bind(':ecomp', $data['employee_comments']);
        $this->db->bind(':plan', $data['development_plan']);
        $this->db->bind(':rating', $data['overall_rating']);
        return $this->db->execute();
    }

    public function updateKpi($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET employee_id = :emp, review_period = :period, kpi_name = :kpi, 
                    target_value = :target, actual_value = :actual, achievement_percent = :achieve, 
                    weight = :weight, manager_evaluation = :meval, employee_comments = :ecomp, 
                    development_plan = :plan, overall_rating = :rating 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':period', $data['review_period']);
        $this->db->bind(':kpi', $data['kpi_name']);
        $this->db->bind(':target', $data['target_value']);
        $this->db->bind(':actual', $data['actual_value']);
        $this->db->bind(':achieve', $data['achievement_percent']);
        $this->db->bind(':weight', $data['weight']);
        $this->db->bind(':meval', $data['manager_evaluation']);
        $this->db->bind(':ecomp', $data['employee_comments']);
        $this->db->bind(':plan', $data['development_plan']);
        $this->db->bind(':rating', $data['overall_rating']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteKpi($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}