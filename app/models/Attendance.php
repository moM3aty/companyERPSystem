<?php
// app/models/Attendance.php

class Attendance extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'attendance';$this->autoUpgradeTable();
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
            'company_id'  => "INT DEFAULT 1",
            'employee_id' => "INT NOT NULL",
            'date'        => "DATE NOT NULL",
            'check_in'    => "TIME DEFAULT NULL",
            'check_out'   => "TIME DEFAULT NULL",
            'status'      => "VARCHAR(50) DEFAULT 'present'",
            'notes'       => "TEXT DEFAULT NULL",
            'created_at'  => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $colName =>$colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {$this->db->query("ALTER TABLE `{$this->table}` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllAttendance($date = null) {$sql = "SELECT a.*, e.name as employee_name, e.position 
                FROM {$this->table} a 
                JOIN employees e ON a.employee_id = e.id 
                WHERE a.company_id = :cid ";
        
        if ($date) {$sql .= " AND a.date = :date ";
        }
        
        $sql .= " ORDER BY a.date DESC, a.check_in DESC";
        
        $this->db->query($sql);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        if ($date) {
            $this->db->bind(':date',$date);
        }
        
        return $this->db->resultSet();
    }

    public function getAttendanceById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function checkExists($employeeId,$date, $excludeId = null) {$sql = "SELECT id FROM {$this->table} WHERE employee_id = :emp AND date = :date AND company_id = :cid";
        if ($excludeId) {$sql .= " AND id != :exc";
        }
        $this->db->query($sql);$this->db->bind(':emp', $employeeId);$this->db->bind(':date', $date);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        if ($excludeId) {
            $this->db->bind(':exc',$excludeId);
        }
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function addAttendance($data) {
        $sql = "INSERT INTO {$this->table} (company_id, employee_id, date, check_in, check_out, status, notes) 
                VALUES (:cid, :emp, :date, :in, :out, :status, :notes)";
        $this->db->query($sql);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp',$data['employee_id']);
        $this->db->bind(':date',$data['date']);
        $this->db->bind(':in', !empty($data['check_in']) ? $data['check_in'] : null);$this->db->bind(':out', !empty($data['check_out']) ?$data['check_out'] : null);
        $this->db->bind(':status',$data['status']);
        $this->db->bind(':notes',$data['notes']);
        return $this->db->execute();
    }

    public function updateAttendance($id,$data) {
        $sql = "UPDATE {$this->table} 
                SET employee_id = :emp, date = :date, check_in = :in, check_out = :out, status = :status, notes = :notes 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);$this->db->bind(':emp', $data['employee_id']);$this->db->bind(':date', $data['date']);$this->db->bind(':in', !empty($data['check_in']) ?$data['check_in'] : null);
        $this->db->bind(':out', !empty($data['check_out']) ? $data['check_out'] : null);$this->db->bind(':status', $data['status']);$this->db->bind(':notes', $data['notes']);$this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteAttendance($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}