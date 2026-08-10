<?php
// app/models/Payroll.php

class Payroll extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'payrolls';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // 1. جدول مسيرات الرواتب الرئيسي
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `payrolls` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `company_id` int(11) NOT NULL DEFAULT 1,
                `reference_no` varchar(50) NOT NULL,
                `month` tinyint(2) NOT NULL,
                `year` year(4) NOT NULL,
                `total_employees` int(11) NOT NULL DEFAULT 0,
                `total_net_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
                `status` enum('draft','approved','paid') DEFAULT 'draft',
                `created_by` int(11) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `reference_no` (`reference_no`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        // 2. جدول تفاصيل رواتب الموظفين (كل موظف على حدة)
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `payroll_details` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `payroll_id` int(11) NOT NULL,
                `employee_id` int(11) NOT NULL,
                `employee_name` varchar(100) NOT NULL,
                `base_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
                `deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
                `bonuses` decimal(10,2) NOT NULL DEFAULT 0.00,
                `net_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}
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

    // 🟢 المحرك الآلي لحساب وتوليد الرواتب 🟢
    public function generatePayroll(int $month, int $year, int $userId): int|bool {
        $companyId = Session::get('company_id') ?: 1;

        try {
            $this->db->beginTransaction();

            // 1. جلب الموظفين النشطين
            $this->db->query("SELECT id, name, name_ar, basic_salary FROM employees WHERE status = 'active' AND company_id = :cid");
            $this->db->bind(':cid', $companyId);
            $employees = $this->db->resultSet();

            if (empty($employees)) {
                throw new Exception("لا يوجد موظفين نشطين لإصدار رواتب لهم.");
            }

            // 2. إنشاء الهيكل الأساسي لمسير الرواتب
            $refNo = 'PAY-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . rand(10, 99);
            $this->db->query("INSERT INTO payrolls (company_id, reference_no, month, year, created_by, status) VALUES (:cid, :ref, :month, :year, :user, 'draft')");
            $this->db->bind(':cid', $companyId);
            $this->db->bind(':ref', $refNo);
            $this->db->bind(':month', $month);
            $this->db->bind(':year', $year);
            $this->db->bind(':user', $userId);
            $this->db->execute();
            
            $payrollId = $this->db->lastInsertId();
            $totalNetAmount = 0;
            $totalEmps = 0;

            // 3. حساب راتب كل موظف
            $insertDetailSql = "INSERT INTO payroll_details (payroll_id, employee_id, employee_name, base_salary, deductions, bonuses, net_salary) 
                                VALUES (:pid, :eid, :ename, :base, :deduct, :bonus, :net)";

            foreach ($employees as $emp) {
                $baseSalary = (float)$emp->basic_salary;
                $deductions = 0;
                $bonuses = 0;

                // أ. جلب السلف المعتمدة لهذا الشهر
                $this->db->query("SELECT SUM(amount) as total_advances FROM employee_advances WHERE employee_id = :eid AND deduction_month = :m AND deduction_year = :y AND status = 'approved' AND company_id = :cid");
                $this->db->bind(':eid', $emp->id);
                $this->db->bind(':m', $month);
                $this->db->bind(':y', $year);
                $this->db->bind(':cid', $companyId);
                $adv = $this->db->single();
                if ($adv && $adv->total_advances) {
                    $deductions += (float)$adv->total_advances;
                }

                // ب. جلب الجزاءات والخصومات لهذا الشهر (إن وُجد جدول sanctions)
                try {
                    $this->db->query("SELECT SUM(amount) as total_sanctions FROM sanctions WHERE employee_id = :eid AND MONTH(date) = :m AND YEAR(date) = :y AND type = 'deduction'");
                    $this->db->bind(':eid', $emp->id);
                    $this->db->bind(':m', $month);
                    $this->db->bind(':y', $year);
                    $sanc = $this->db->single();
                    if ($sanc && $sanc->total_sanctions) {
                        $deductions += (float)$sanc->total_sanctions;
                    }
                } catch(Exception $e) {} // تجاهل إذا لم يتم تفعيل الجزاءات بعد

                // ج. حساب الصافي
                $netSalary = $baseSalary + $bonuses - $deductions;
                if ($netSalary < 0) $netSalary = 0;

                $totalNetAmount += $netSalary;
                $totalEmps++;

                // حفظ تفاصيل الموظف
                $this->db->query($insertDetailSql);
                $this->db->bind(':pid', $payrollId);
                $this->db->bind(':eid', $emp->id);
                $this->db->bind(':ename', $emp->name_ar ?: $emp->name);
                $this->db->bind(':base', $baseSalary);
                $this->db->bind(':deduct', $deductions);
                $this->db->bind(':bonus', $bonuses);
                $this->db->bind(':net', $netSalary);
                $this->db->execute();
            }

            // 4. تحديث الإجمالي في الجدول الرئيسي
            $this->db->query("UPDATE payrolls SET total_employees = :emps, total_net_amount = :net WHERE id = :id");
            $this->db->bind(':emps', $totalEmps);
            $this->db->bind(':net', $totalNetAmount);
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
        $this->db->beginTransaction();
        try {
            $this->db->query("UPDATE payrolls SET status = :status WHERE id = :id AND company_id = :cid");
            $this->db->bind(':status', $status);
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            // إذا تم اعتماد الرواتب ودفعها، نقوم بتحويل حالة السلف إلى "مخصومة"
            if ($status === 'paid' || $status === 'approved') {
                $payroll = $this->getPayrollById($id);
                if ($payroll) {
                    $this->db->query("UPDATE employee_advances SET status = 'deducted' 
                                      WHERE deduction_month = :m AND deduction_year = :y AND status = 'approved' AND company_id = :cid");
                    $this->db->bind(':m', $payroll->month);
                    $this->db->bind(':y', $payroll->year);
                    $this->db->bind(':cid', Session::get('company_id') ?: 1);
                    $this->db->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deletePayroll(int $id): bool {
        try {
            $this->db->query("DELETE FROM payroll_details WHERE payroll_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
        } catch(Exception $e) {}

        $this->db->query("DELETE FROM payrolls WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}