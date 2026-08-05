<?php
// app/models/Payroll.php

class Payroll extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'payrolls';
    }

    /**
     * جلب جميع مسيرات الرواتب
     */
    public function getAllPayrolls(): array {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * جلب تفاصيل مسير محدد بالـ ID
     */
    public function getPayrollById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * جلب تفاصيل الموظفين داخل المسير
     */
    public function getPayrollDetails(int $payrollId): array {
        $this->db->query("SELECT * FROM payroll_details WHERE payroll_id = :pid");
        $this->db->bind(':pid', $payrollId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * حفظ مسير الرواتب والتفاصيل باستخدام Transactions
     */
    public function createPayroll(array $data, array $details): bool {
        try {
            $this->db->beginTransaction();

            // 1. توليد رقم مرجعي (Reference Number)
            $refNo = 'PAY-' . str_pad((string)$data['year'], 4, '0', STR_PAD_LEFT) . str_pad((string)$data['month'], 2, '0', STR_PAD_LEFT) . '-' . random_int(10, 99);

            // 2. إدخال السجل الرئيسي للمسير
            $sqlMain = "INSERT INTO {$this->table} (reference_no, month, year, total_employees, total_net_amount, status, created_by, created_at) 
                        VALUES (:ref_no, :month, :year, :total_emp, :total_net, 'approved', :created_by, NOW())";
            
            $this->db->query($sqlMain);
            $this->db->bind(':ref_no', $refNo);
            $this->db->bind(':month', $data['month'], PDO::PARAM_INT);
            $this->db->bind(':year', $data['year'], PDO::PARAM_INT);
            $this->db->bind(':total_emp', count($details), PDO::PARAM_INT);
            $this->db->bind(':total_net', $data['total_net_amount']);
            $this->db->bind(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
            $this->db->execute();

            $payrollId = (int)$this->db->lastInsertId();

            // 3. إدخال تفاصيل رواتب كل موظف
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

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}