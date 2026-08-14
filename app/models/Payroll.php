<?php
// app/models/Payroll.php

class Payroll extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'payrolls';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // جدول مسيرات الرواتب الرئيسي
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `payrolls` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $payrollCols = [
            'company_id'       => "INT NOT NULL DEFAULT 1",
            'reference_no'     => "VARCHAR(50) NOT NULL",
            'month'            => "TINYINT(2) NOT NULL",
            'year'             => "YEAR(4) NOT NULL",
            'total_employees'  => "INT(11) NOT NULL DEFAULT 0",
            'total_net_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'status'           => "VARCHAR(50) DEFAULT 'draft'", // draft, approved, paid
            'created_by'       => "INT(11) NOT NULL",
            'created_at'       => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($payrollCols as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `payrolls` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `payrolls` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }

        // جدول تفاصيل رواتب الموظفين (كل موظف سطر)
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `payroll_details` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $detailCols = [
            'payroll_id'         => "INT NOT NULL",
            'employee_id'        => "INT NOT NULL",
            'employee_name'      => "VARCHAR(100) NOT NULL",
            'base_salary'        => "DECIMAL(10,2) NOT NULL DEFAULT 0.00",
            'housing_allowance'  => "DECIMAL(10,2) DEFAULT 0.00",
            'transport_allowance'=> "DECIMAL(10,2) DEFAULT 0.00",
            'other_allowances'   => "DECIMAL(10,2) DEFAULT 0.00",
            'overtime_amount'    => "DECIMAL(10,2) DEFAULT 0.00",
            'commissions'        => "DECIMAL(10,2) DEFAULT 0.00", 
            'advance_deduction'  => "DECIMAL(10,2) DEFAULT 0.00", 
            'sanction_deduction' => "DECIMAL(10,2) DEFAULT 0.00", 
            'absence_deduction'  => "DECIMAL(10,2) DEFAULT 0.00", 
            'net_salary'         => "DECIMAL(10,2) NOT NULL DEFAULT 0.00"
        ];

        foreach ($detailCols as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `payroll_details` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `payroll_details` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllPayrolls(): array {
        $sql = "SELECT p.*, u.name as creator_name 
                FROM payrolls p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.company_id = :cid 
                ORDER BY p.year DESC, p.month DESC, p.id DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getPayrollById(int $id): ?object {
        $this->db->query("SELECT * FROM payrolls WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function getPayrollDetails(int $payrollId): array {
        $this->db->query("SELECT pd.*, e.employee_number 
                          FROM payroll_details pd 
                          LEFT JOIN employees e ON pd.employee_id = e.id 
                          WHERE pd.payroll_id = :pid");
        $this->db->bind(':pid', $payrollId);
        return $this->db->resultSet();
    }

    public function checkExists(int $month, int $year): bool {
        $this->db->query("SELECT id FROM payrolls WHERE month = :month AND year = :year AND company_id = :cid");
        $this->db->bind(':month', $month);
        $this->db->bind(':year', $year);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    // 🟢 الدالة الأساسية لتوليد المسير تلقائياً 🟢
    public function generatePayroll(int $month, int $year, int $userId) {
        $companyId = Session::get('company_id') ?: 1;
        $this->db->beginTransaction();

        try {
            // 1. جلب الموظفين النشطين
            $this->db->query("SELECT * FROM employees WHERE company_id = :cid AND employment_status = 'Active'");
            $this->db->bind(':cid', $companyId);
            $employees = $this->db->resultSet();

            if (empty($employees)) {
                $this->db->rollBack();
                return false;
            }

            // 2. إنشاء رأس مسير الرواتب (مبدئياً بصفر)
            $ref = 'PR-' . $year . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . '-' . time();
            $this->db->query("INSERT INTO payrolls (company_id, reference_no, month, year, created_by) 
                              VALUES (:cid, :ref, :month, :year, :user)");
            $this->db->bind(':cid', $companyId);
            $this->db->bind(':ref', $ref);
            $this->db->bind(':month', $month);
            $this->db->bind(':year', $year);
            $this->db->bind(':user', $userId);
            $this->db->execute();
            
            $payrollId = $this->db->lastInsertId();

            $totalEmployees = 0;
            $grandTotalNet = 0;

            // 3. حساب راتب كل موظف
            $insertDetailSql = "INSERT INTO payroll_details 
                                (payroll_id, employee_id, employee_name, base_salary, housing_allowance, transport_allowance, other_allowances, 
                                 overtime_amount, commissions, advance_deduction, sanction_deduction, absence_deduction, net_salary) 
                                VALUES (:pid, :eid, :ename, :base, :house, :trans, :other, :overtime, :comm, :adv, :sanc, :abs, :net)";

            foreach ($employees as $emp) {
                // الحسابات الأساسية
                $base = (float)$emp->basic_salary;
                $house = (float)$emp->housing_allowance;
                $trans = (float)$emp->transport_allowance;
                $other = (float)$emp->other_allowances;
                
                $grossSalary = $base + $house + $trans + $other;
                $dailyRate = $base > 0 ? ($base / 30) : 0; // لتبسيط خصم الغياب

                // 🟢 استخراج البيانات الديناميكية للشهر المحدد (تتطلب جداولها) 🟢
                $overtimeAmount = 0;
                $commissions = 0;
                $advanceDeduction = 0;
                $sanctionDeduction = 0;
                $absenceDeduction = 0;

                // 1. حساب خصم الغيابات (Absence)
                try {
                    $this->db->query("SELECT COUNT(id) as absent_days FROM attendance WHERE employee_id = :eid AND status = 'absent' AND MONTH(date) = :m AND YEAR(date) = :y");
                    $this->db->bind(':eid', $emp->id); $this->db->bind(':m', $month); $this->db->bind(':y', $year);
                    $absData = $this->db->single();
                    if ($absData && $absData->absent_days > 0) {
                        $absenceDeduction = $absData->absent_days * $dailyRate;
                    }
                } catch(Exception $e) {}

                // صافي الراتب
                $totalAdditions = $grossSalary + $overtimeAmount + $commissions;
                $totalDeductions = $advanceDeduction + $sanctionDeduction + $absenceDeduction;
                $netSalary = $totalAdditions - $totalDeductions;
                
                if ($netSalary < 0) $netSalary = 0; // لا يمكن أن يكون الراتب بالسالب

                // إدخال التفاصيل
                $this->db->query($insertDetailSql);
                $this->db->bind(':pid', $payrollId);
                $this->db->bind(':eid', $emp->id);
                $this->db->bind(':ename', $emp->full_name);
                $this->db->bind(':base', $base);
                $this->db->bind(':house', $house);
                $this->db->bind(':trans', $trans);
                $this->db->bind(':other', $other);
                $this->db->bind(':overtime', $overtimeAmount);
                $this->db->bind(':comm', $commissions);
                $this->db->bind(':adv', $advanceDeduction);
                $this->db->bind(':sanc', $sanctionDeduction);
                $this->db->bind(':abs', $absenceDeduction);
                $this->db->bind(':net', $netSalary);
                $this->db->execute();

                $totalEmployees++;
                $grandTotalNet += $netSalary;
            }

            // 4. تحديث الرأس بالمجاميع النهائية
            $this->db->query("UPDATE payrolls SET total_employees = :emps, total_net_amount = :net WHERE id = :id");
            $this->db->bind(':emps', $totalEmployees);
            $this->db->bind(':net', $grandTotalNet);
            $this->db->bind(':id', $payrollId);
            $this->db->execute();

            $this->db->commit();
            return $payrollId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateStatus(int $id, string $status): bool {
        $this->db->query("UPDATE payrolls SET status = :status WHERE id = :id AND company_id = :cid");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deletePayroll(int $id): bool {
        $this->db->beginTransaction();
        try {
            $this->db->query("DELETE FROM payroll_details WHERE payroll_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->query("DELETE FROM payrolls WHERE id = :id AND company_id = :cid");
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}