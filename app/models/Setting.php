<?php
// app/models/Setting.php

class Setting extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'settings';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `company_id` int(11) DEFAULT 1,
                `setting_key` varchar(100) NOT NULL,
                `setting_value` text NULL,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_key_company` (`company_id`, `setting_key`)
            )");
            $this->db->execute();
            
            // إدخال الإعدادات الافتراضية إذا كان الجدول فارغاً
            $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
            $count = $this->db->single()->count;
            if ($count == 0) {
                $defaults = [
                    'company_name' => 'شركة نور التجارية',
                    'legal_entity' => 'شركة ذات مسؤولية محدودة (LLC)',
                    'tax_number' => '300000000000003',
                    'commercial_registration' => '1010000000',
                    'company_address' => 'المملكة العربية السعودية - الرياض',
                    'base_currency' => 'SAR',
                    'fiscal_year_start' => date('Y-01-01'),
                    'fiscal_year_end' => date('Y-12-31'),
                    'accounting_basis' => 'Accrual', // الاستحقاق
                    'default_tax_rate' => '15',
                    'invoice_prefix' => 'INV-',
                    'payment_terms' => 'Net 30 Days'
                ];
                foreach ($defaults as $k => $v) {
                    $this->db->query("INSERT INTO {$this->table} (company_id, setting_key, setting_value) VALUES (1, :k, :v)");
                    $this->db->bind(':k', $k);
                    $this->db->bind(':v', $v);
                    $this->db->execute();
                }
            }
        } catch (Exception $e) {}
    }

    // جلب جميع الإعدادات للشركة الحالية وتحويلها إلى مصفوفة (Array) سهلة الاستخدام
    public function getAllSettings() {
        $this->db->query("SELECT setting_key, setting_value FROM {$this->table} WHERE company_id = :cid OR company_id IS NULL");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $results = $this->db->resultSet();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
        return $settings;
    }

    // تحديث إعداد معين أو إنشائه إن لم يكن موجوداً
    public function saveSettings($dataArray) {
        $cid = Session::get('company_id') ?: 1;
        $this->db->beginTransaction();
        try {
            foreach ($dataArray as $key => $value) {
                // نستخدم استعلام الإدراج مع التحديث في حالة وجود المفتاح (Upsert)
                $sql = "INSERT INTO {$this->table} (company_id, setting_key, setting_value) 
                        VALUES (:cid, :key, :val) 
                        ON DUPLICATE KEY UPDATE setting_value = :val2";
                $this->db->query($sql);
                $this->db->bind(':cid', $cid);
                $this->db->bind(':key', $key);
                $this->db->bind(':val', $value);
                $this->db->bind(':val2', $value);
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