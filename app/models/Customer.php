<?php
// app/models/Customer.php

class Customer extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'customers';
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
            'customer_number' => "VARCHAR(50) NOT NULL",
            'name'            => "VARCHAR(150) NOT NULL",
            'company_name'    => "VARCHAR(150) NULL",
            'vat_number'      => "VARCHAR(100) NULL",
            'address'         => "TEXT NULL",
            'contact_person'  => "VARCHAR(150) NULL",
            'phone'           => "VARCHAR(50) NULL",
            'email'           => "VARCHAR(100) NULL",
            'credit_limit'    => "DECIMAL(15,2) DEFAULT 0.00",
            'payment_terms'   => "VARCHAR(100) NULL",
            'currency'        => "VARCHAR(10) DEFAULT 'SAR'",
            'opening_balance' => "DECIMAL(15,2) DEFAULT 0.00",
            'current_balance' => "DECIMAL(15,2) DEFAULT 0.00",
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

    public function getAllCustomers() {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY id DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getCustomerById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createCustomer($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, customer_number, name, company_name, vat_number, address, contact_person, phone, email, credit_limit, payment_terms, currency, opening_balance, current_balance) 
                VALUES 
                (:cid, :cnum, :name, :company, :vat, :address, :contact, :phone, :email, :limit, :terms, :curr, :open_bal, :open_bal)";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':cnum', $data['customer_number']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':company', $data['company_name']);
        $this->db->bind(':vat', $data['vat_number']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':contact', $data['contact_person']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':limit', $data['credit_limit']);
        $this->db->bind(':terms', $data['payment_terms']);
        $this->db->bind(':curr', $data['currency']);
        $this->db->bind(':open_bal', $data['opening_balance']);
        
        return $this->db->execute();
    }

    public function updateCustomer($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name, company_name = :company, vat_number = :vat, address = :address, 
                contact_person = :contact, phone = :phone, email = :email, credit_limit = :limit, 
                payment_terms = :terms, currency = :curr 
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':company', $data['company_name']);
        $this->db->bind(':vat', $data['vat_number']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':contact', $data['contact_person']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':limit', $data['credit_limit']);
        $this->db->bind(':terms', $data['payment_terms']);
        $this->db->bind(':curr', $data['currency']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        
        return $this->db->execute();
    }

    public function deleteCustomer($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    // جلب كشف حساب العميل (الحركات المالية)
    public function getCustomerStatement($customerId) {
        $sql = "SELECT 'فاتورة' as type, invoice_number as ref, created_at as date, grand_total as amount, 'debit' as action 
                FROM invoices WHERE customer_id = :cid1 
                UNION ALL 
                SELECT 'سند قبض' as type, voucher_number as ref, payment_date as date, amount, 'credit' as action 
                FROM payments WHERE customer_id = :cid2 AND payment_type = 'In'
                ORDER BY date ASC";
        
        // إذا لم يتم بناء جداول الفواتير بعد، نستخدم استعلام محمي
        try {
            $this->db->query($sql);
            $this->db->bind(':cid1', $customerId);
            $this->db->bind(':cid2', $customerId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    // جلب تقرير أعمار الديون (Aging Report)
    public function getCustomerAging($customerId) {
        $sql = "SELECT 
                    SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) <= 0 THEN (grand_total - amount_paid) ELSE 0 END) as current_due,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 1 AND 30 THEN (grand_total - amount_paid) ELSE 0 END) as days_30,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 31 AND 60 THEN (grand_total - amount_paid) ELSE 0 END) as days_60,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), due_date) > 60 THEN (grand_total - amount_paid) ELSE 0 END) as days_90_plus
                FROM invoices 
                WHERE customer_id = :cid AND payment_status != 'Paid'";
        
        try {
            $this->db->query($sql);
            $this->db->bind(':cid', $customerId);
            return $this->db->single();
        } catch (Exception $e) {
            return (object)['current_due'=>0, 'days_30'=>0, 'days_60'=>0, 'days_90_plus'=>0];
        }
    }
}