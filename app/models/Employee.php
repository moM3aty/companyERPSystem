<?php
// app/models/Employee.php

class Employee extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employees';
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
            'employee_number'     => "VARCHAR(50) NULL",
            'full_name'           => "VARCHAR(150) NOT NULL",
            'name_ar'             => "VARCHAR(150) NULL",
            'name_en'             => "VARCHAR(150) NULL",
            'gender'              => "VARCHAR(20) DEFAULT 'Male'",
            'dob'                 => "DATE NULL",
            'nationality'         => "VARCHAR(100) NULL",
            'marital_status'      => "VARCHAR(50) DEFAULT 'Single'",
            'photograph'          => "VARCHAR(255) NULL",
            'blood_group'         => "VARCHAR(10) NULL",
            'personal_email'      => "VARCHAR(100) NULL",
            'personal_mobile'     => "VARCHAR(50) NULL",
            'emergency_contact'   => "VARCHAR(150) NULL",
            'emergency_relation'  => "VARCHAR(100) NULL",
            'emergency_phone'     => "VARCHAR(50) NULL",
            
            // Employment Information
            'branch_id'           => "INT NULL",
            'department_id'       => "INT NULL",
            'job_title'           => "VARCHAR(150) NULL",
            'position'            => "VARCHAR(150) NULL",
            'employee_category'   => "VARCHAR(100) NULL",
            'employment_type'     => "VARCHAR(50) DEFAULT 'Full-time'",
            'direct_manager_id'   => "INT NULL",
            'date_of_joining'     => "DATE NULL",
            'probation_start_date'=> "DATE NULL",
            'probation_end_date'  => "DATE NULL",
            'employment_status'   => "VARCHAR(50) DEFAULT 'Active'",
            'work_location'       => "VARCHAR(150) NULL",
            'cost_center'         => "VARCHAR(100) NULL",
            'project_assignment'  => "VARCHAR(150) NULL",
            'employee_grade'      => "VARCHAR(50) NULL",
            
            // Bank & Payroll Information
            'bank_name'           => "VARCHAR(150) NULL",
            'account_holder'      => "VARCHAR(150) NULL",
            'iban'                => "VARCHAR(100) NULL",
            'salary_payment_method'=> "VARCHAR(50) DEFAULT 'Bank Transfer'",
            'basic_salary'        => "DECIMAL(15,2) DEFAULT 0.00",
            'housing_allowance'   => "DECIMAL(15,2) DEFAULT 0.00",
            'transport_allowance' => "DECIMAL(15,2) DEFAULT 0.00",
            'other_allowances'    => "DECIMAL(15,2) DEFAULT 0.00",
            
            // Separation / Exit
            'resignation_date'    => "DATE NULL",
            'last_working_day'    => "DATE NULL",
            'reason_for_leaving'  => "TEXT NULL",
            
            'created_at'          => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllEmployees() {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY id DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getEmployeeById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    // إضافة الحماية try-catch لمنع الانهيار
    public function getEmployeeDocuments($employeeId) {
        try {
            $this->db->query("SELECT *, DATEDIFF(expiry_date, CURDATE()) as days_to_expire 
                              FROM hr_employee_documents 
                              WHERE employee_id = :emp AND company_id = :cid ORDER BY expiry_date ASC");
            $this->db->bind(':emp', $employeeId);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getEmployeeAssets($employeeId) {
        try {
            $this->db->query("SELECT * FROM hr_employee_assets 
                              WHERE employee_id = :emp AND company_id = :cid ORDER BY issue_date DESC");
            $this->db->bind(':emp', $employeeId);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getEmployeeLeaves($employeeId) {
        try {
            $this->db->query("SELECT *, DATEDIFF(end_date, start_date) + 1 as total_days 
                              FROM leave_requests 
                              WHERE employee_id = :emp AND company_id = :cid ORDER BY created_at DESC");
            $this->db->bind(':emp', $employeeId);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function createEmployee($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_number, full_name, name_ar, name_en, gender, dob, nationality, marital_status, 
                 blood_group, personal_email, personal_mobile, emergency_contact, emergency_relation, emergency_phone, 
                 job_title, position, employee_category, employment_type, date_of_joining, probation_start_date, probation_end_date, 
                 employment_status, work_location, cost_center, project_assignment, employee_grade, 
                 bank_name, account_holder, iban, salary_payment_method, basic_salary, housing_allowance, transport_allowance, other_allowances) 
                VALUES 
                (:cid, :emp_num, :fname, :name_ar, :name_en, :gender, :dob, :nat, :marital, 
                 :blood, :email, :mobile, :em_contact, :em_rel, :em_phone, 
                 :job, :pos, :cat, :emp_type, :join_date, :prob_start, :prob_end, 
                 :status, :loc, :cost, :proj, :grade, 
                 :bank, :holder, :iban, :pay_method, :salary, :house, :trans, :other)";
                 
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp_num', $data['employee_number']);
        $this->db->bind(':fname', $data['full_name']);
        $this->db->bind(':name_ar', $data['name_ar']);
        $this->db->bind(':name_en', $data['name_en']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':dob', !empty($data['dob']) ? $data['dob'] : null);
        $this->db->bind(':nat', $data['nationality']);
        $this->db->bind(':marital', $data['marital_status']);
        $this->db->bind(':blood', $data['blood_group']);
        $this->db->bind(':email', $data['personal_email']);
        $this->db->bind(':mobile', $data['personal_mobile']);
        $this->db->bind(':em_contact', $data['emergency_contact']);
        $this->db->bind(':em_rel', $data['emergency_relation']);
        $this->db->bind(':em_phone', $data['emergency_phone']);
        
        $this->db->bind(':job', $data['job_title']);
        $this->db->bind(':pos', $data['position']);
        $this->db->bind(':cat', $data['employee_category']);
        $this->db->bind(':emp_type', $data['employment_type']);
        $this->db->bind(':join_date', !empty($data['date_of_joining']) ? $data['date_of_joining'] : null);
        $this->db->bind(':prob_start', !empty($data['probation_start_date']) ? $data['probation_start_date'] : null);
        $this->db->bind(':prob_end', !empty($data['probation_end_date']) ? $data['probation_end_date'] : null);
        $this->db->bind(':status', $data['employment_status']);
        $this->db->bind(':loc', $data['work_location']);
        $this->db->bind(':cost', $data['cost_center']);
        $this->db->bind(':proj', $data['project_assignment']);
        $this->db->bind(':grade', $data['employee_grade']);
        
        $this->db->bind(':bank', $data['bank_name']);
        $this->db->bind(':holder', $data['account_holder']);
        $this->db->bind(':iban', $data['iban']);
        $this->db->bind(':pay_method', $data['salary_payment_method']);
        $this->db->bind(':salary', $data['basic_salary']);
        $this->db->bind(':house', $data['housing_allowance']);
        $this->db->bind(':trans', $data['transport_allowance']);
        $this->db->bind(':other', $data['other_allowances']);
        
        if ($this->db->execute()) return (int)$this->db->lastInsertId();
        return false;
    }

    public function updateEmployee($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                full_name = :fname, name_ar = :name_ar, name_en = :name_en, gender = :gender, 
                dob = :dob, nationality = :nat, marital_status = :marital, blood_group = :blood,
                personal_mobile = :mobile, personal_email = :email, 
                emergency_contact = :em_contact, emergency_relation = :em_rel, emergency_phone = :em_phone,
                job_title = :job, position = :pos, employee_category = :cat, employment_type = :emp_type,
                date_of_joining = :join_date, probation_start_date = :prob_start, probation_end_date = :prob_end,
                employment_status = :status, work_location = :loc, cost_center = :cost, 
                project_assignment = :proj, employee_grade = :grade,
                bank_name = :bank, account_holder = :holder, iban = :iban, salary_payment_method = :pay_method,
                basic_salary = :salary, housing_allowance = :house, transport_allowance = :trans, other_allowances = :other
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':fname', $data['full_name']);
        $this->db->bind(':name_ar', $data['name_ar']);
        $this->db->bind(':name_en', $data['name_en']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':dob', !empty($data['dob']) ? $data['dob'] : null);
        $this->db->bind(':nat', $data['nationality']);
        $this->db->bind(':marital', $data['marital_status']);
        $this->db->bind(':blood', $data['blood_group']);
        
        $this->db->bind(':mobile', $data['personal_mobile']);
        $this->db->bind(':email', $data['personal_email']);
        $this->db->bind(':em_contact', $data['emergency_contact']);
        $this->db->bind(':em_rel', $data['emergency_relation']);
        $this->db->bind(':em_phone', $data['emergency_phone']);
        
        $this->db->bind(':job', $data['job_title']);
        $this->db->bind(':pos', $data['position']);
        $this->db->bind(':cat', $data['employee_category']);
        $this->db->bind(':emp_type', $data['employment_type']);
        $this->db->bind(':join_date', !empty($data['date_of_joining']) ? $data['date_of_joining'] : null);
        $this->db->bind(':prob_start', !empty($data['probation_start_date']) ? $data['probation_start_date'] : null);
        $this->db->bind(':prob_end', !empty($data['probation_end_date']) ? $data['probation_end_date'] : null);
        $this->db->bind(':status', $data['employment_status']);
        $this->db->bind(':loc', $data['work_location']);
        $this->db->bind(':cost', $data['cost_center']);
        $this->db->bind(':proj', $data['project_assignment']);
        $this->db->bind(':grade', $data['employee_grade']);
        
        $this->db->bind(':bank', $data['bank_name']);
        $this->db->bind(':holder', $data['account_holder']);
        $this->db->bind(':iban', $data['iban']);
        $this->db->bind(':pay_method', $data['salary_payment_method']);
        $this->db->bind(':salary', $data['basic_salary']);
        $this->db->bind(':house', $data['housing_allowance']);
        $this->db->bind(':trans', $data['transport_allowance']);
        $this->db->bind(':other', $data['other_allowances']);
        
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteEmployee($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}