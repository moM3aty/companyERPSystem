<?php
// app/models/Quote.php

class Quote extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'quotes';
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
            'company_id'   => "INT DEFAULT 1",
            'quote_number' => "VARCHAR(50) NOT NULL",
            'customer_id'  => "INT NULL",
            'lead_id'      => "INT NULL",
            'quote_date'   => "DATE NOT NULL",
            'expiry_date'  => "DATE NULL",
            'status'       => "VARCHAR(50) DEFAULT 'Draft'",
            'total_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'notes'        => "TEXT NULL",
            'created_by'   => "INT NOT NULL",
            'created_at'   => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `quote_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $itemColumns = [
            'quote_id'     => "INT NOT NULL",
            'product_id'   => "INT NOT NULL", // 🟢 الحقل الإلزامي الجديد
            'product_name' => "VARCHAR(255) NOT NULL",
            'quantity'     => "DECIMAL(10,2) NOT NULL DEFAULT 1.00",
            'unit_price'   => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'total_price'  => "DECIMAL(15,2) NOT NULL DEFAULT 0.00"
        ];

        foreach ($itemColumns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `quote_items` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `quote_items` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllQuotes() {
        try {
            $sql = "SELECT q.*, c.name as customer_name, l.name as lead_name, u.name as user_name 
                    FROM {$this->table} q 
                    LEFT JOIN customers c ON q.customer_id = c.id 
                    LEFT JOIN leads l ON q.lead_id = l.id 
                    LEFT JOIN users u ON q.created_by = u.id 
                    WHERE q.company_id = :cid ORDER BY q.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getQuoteById($id) {
        try {
            $sql = "SELECT q.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone, 
                           l.name as lead_name, l.email as lead_email, l.phone as lead_phone, u.name as user_name 
                    FROM {$this->table} q 
                    LEFT JOIN customers c ON q.customer_id = c.id 
                    LEFT JOIN leads l ON q.lead_id = l.id 
                    LEFT JOIN users u ON q.created_by = u.id 
                    WHERE q.id = :id AND q.company_id = :cid LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getQuoteItems($quoteId) {
        try {
            $this->db->query("SELECT * FROM quote_items WHERE quote_id = :qid");
            $this->db->bind(':qid', $quoteId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function createQuote($data, $items) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} 
                    (company_id, quote_number, customer_id, lead_id, quote_date, expiry_date, status, total_amount, notes, created_by) 
                    VALUES (:cid, :qnum, :cust_id, :lead_id, :qdate, :edate, 'Draft', :total, :notes, :user)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':qnum', $data['quote_number']);
            $this->db->bind(':cust_id', !empty($data['customer_id']) ? $data['customer_id'] : null);
            $this->db->bind(':lead_id', !empty($data['lead_id']) ? $data['lead_id'] : null);
            $this->db->bind(':qdate', $data['quote_date']);
            $this->db->bind(':edate', !empty($data['expiry_date']) ? $data['expiry_date'] : null);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();
            
            $quoteId = $this->db->lastInsertId();

            // 🟢 ربط تفاصيل عرض السعر بـ product_id 🟢
            $sqlItem = "INSERT INTO quote_items (quote_id, product_id, product_name, quantity, unit_price, total_price) 
                        VALUES (:qid, :pid, :pname, :qty, :price, :ptotal)";
            
            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':qid', $quoteId);
                $this->db->bind(':pid', $item['product_id']);
                $this->db->bind(':pname', $item['product_name']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':price', $item['unit_price']);
                $this->db->bind(':ptotal', $item['total_price']);
                $this->db->execute();
            }

            $this->db->commit();
            return $quoteId;

        } catch (Throwable $e) {
            try { $this->db->rollBack(); } catch (Throwable $t) {}
            throw new Exception($e->getMessage()); 
        }
    }

    public function deleteQuote($id) {
        $this->db->beginTransaction();
        try {
            $this->db->query("DELETE FROM quote_items WHERE quote_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
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