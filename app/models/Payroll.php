<?php
// app/models/Payroll.php

class Payroll extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'payrolls';
    }

    public function getAllPayrolls(): array {
        $sql = "SELECT p.*, u.name as created_by_name 
                FROM {$this->table} p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.company_id = :cid
                ORDER BY p.year DESC, p.month DESC, p.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getPayrollById(int $id): ?object {
        $sql = "SELECT p.*, u.name as created_by_name 
                FROM {$this->table} p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.id = :id AND p.company_id = :cid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getPayrollDetails(int $payrollId): array {
        // نتحقق أولاً من صلاحية الشركة
        if (!$this->getPayrollById($payrollId)) return [];

        $sql = "SELECT * FROM payroll_details WHERE payroll_id = :pid";
        $this->db->query($sql);
        $this->db->bind(':pid', $payrollId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function createPayroll(array $data, array $details): bool {
        try {
            $this->db->beginTransaction();
            $companyId = Session::get('company_id');
            $refNo = 'PAY-' . str_pad((string)$data['year'], 4, '0', STR_PAD_LEFT) . str_pad((string)$data['month'], 2, '0', STR_PAD_LEFT) . '-' . random_int(10, 99);

            $sqlMain = "INSERT INTO {$this->table} (company_id, reference_no, month, year, total_employees, total_net_amount, status, created_by, created_at) 
                        VALUES (:cid, :ref_no, :month, :year, :total_emp, :total_net, 'approved', :created_by, NOW())";
            
            $this->db->query($sqlMain);
            $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
            $this->db->bind(':ref_no', $refNo);
            $this->db->bind(':month', $data['month'], PDO::PARAM_INT);
            $this->db->bind(':year', $data['year'], PDO::PARAM_INT);
            $this->db->bind(':total_emp', count($details), PDO::PARAM_INT);
            $this->db->bind(':total_net', $data['total_net_amount']);
            $this->db->bind(':created_by', $data['created_by'], PDO::PARAM_INT);
            $this->db->execute();

            $payrollId = (int)$this->db->lastInsertId();

            $sqlDetails = "INSERT INTO payroll_details (payroll_id, employee_id, employee_name, base_salary, deductions, bonuses, net_salary) 
                           VALUES (:pid, :eid, :ename, :base, :ded, :bon, :net)";
            
            foreach ($details as $d) {
                $this->db->query($sqlDetails);
                $this->db->bind(':pid', $payrollId, PDO::PARAM_INT);
                $this->db->bind(':eid', $d['employee_id'], PDO::PARAM_INT);
                $this->db->bind(':ename', $d['employee_name']);
                $this->db->bind(':base', $d['base_salary']);
                $this->db->bind(':ded', $d['deductions']);
                $this->db->bind(':bon', $d['bonuses']);
                $this->db->bind(':net', $d['net_salary']);
                $this->db->execute();
            }

            // تحديث حالة السلف للشركة الحالية فقط
            $sqlAdv = "UPDATE employee_advances SET status = 'deducted' 
                       WHERE deduction_month = :m AND deduction_year = :y AND status = 'approved' AND company_id = :cid";
            $this->db->query($sqlAdv);
            $this->db->bind(':m', $data['month'], PDO::PARAM_INT);
            $this->db->bind(':y', $data['year'], PDO::PARAM_INT);
            $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
            $this->db->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}