<?php
// app/models/Advance.php
class Advance extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_advances';
    }
    public function getAllAdvances(): array {
        $sql = "SELECT a.*, e.name as employee_name, e.salary 
                FROM {$this->table} a 
                LEFT JOIN employees e ON a.employee_id = e.id 
                ORDER BY a.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    public function createAdvance(array $data): bool {
        $sql = "INSERT INTO {$this->table} (employee_id, amount, date, reason, deduction_month, deduction_year, status) 
                VALUES (:employee_id, :amount, :date, :reason, :deduction_month, :deduction_year, 'pending')";
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':deduction_month', $data['deduction_month'], PDO::PARAM_INT);
        $this->db->bind(':deduction_year', $data['deduction_year'], PDO::PARAM_INT);
        return $this->db->execute();
    }
    public function updateStatus(int $id, string $status, int $adminId): bool {
        $sql = "UPDATE {$this->table} SET status = :status, approved_by = :admin_id WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':admin_id', $adminId, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    public function getAdvanceById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    public function updateAdvance(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} SET employee_id = :employee_id, amount = :amount, date = :date, reason = :reason, deduction_month = :deduction_month, deduction_year = :deduction_year WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':deduction_month', $data['deduction_month'], PDO::PARAM_INT);
        $this->db->bind(':deduction_year', $data['deduction_year'], PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    public function deleteAdvance(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}