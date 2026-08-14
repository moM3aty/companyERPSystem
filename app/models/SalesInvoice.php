<?php
// app/models/SalesInvoice.php

class SalesInvoice extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sales_invoices';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // Main Sales Invoices Table
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'      => "INT DEFAULT 1",
            'invoice_number'  => "VARCHAR(50) NOT NULL",
            'customer_id'     => "INT NOT NULL",
            'so_id'           => "INT NULL", // ارتباط بأمر البيع
            'invoice_date'    => "DATE NOT NULL",
            'due_date'        => "DATE NOT NULL",
            'subtotal'        => "DECIMAL(15,2) DEFAULT 0.00",
            'discount'        => "DECIMAL(15,2) DEFAULT 0.00",
            'tax_amount'      => "DECIMAL(15,2) DEFAULT 0.00",
            'grand_total'     => "DECIMAL(15,2) DEFAULT 0.00",
            'amount_paid'     => "DECIMAL(15,2) DEFAULT 0.00",
            'payment_status'  => "VARCHAR(50) DEFAULT 'Unpaid'", // Unpaid, Partial, Paid
            'notes'           => "TEXT NULL",
            'created_by'      => "INT NOT NULL",
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

        // Sales Invoice Items Table
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `sales_invoice_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `invoice_id` int(11) NOT NULL,
                `product_id` int(11) NULL,
                `description` varchar(255) NOT NULL,
                `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
                `unit_price` decimal(15,2) DEFAULT 0.00,
                `discount` decimal(15,2) DEFAULT 0.00,
                `tax_rate` decimal(5,2) DEFAULT 15.00,
                `subtotal` decimal(15,2) DEFAULT 0.00,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}
    }

    public function getAllInvoices() {
        $sql = "SELECT si.*, c.name as customer_name, so.so_number 
                FROM {$this->table} si 
                LEFT JOIN customers c ON si.customer_id = c.id 
                LEFT JOIN sales_orders so ON si.so_id = so.id 
                WHERE si.company_id = :cid ORDER BY si.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getInvoiceById($id) {
        $this->db->query("SELECT si.*, c.name as customer_name, c.company_name, c.vat_number as customer_vat, c.address as customer_address, so.so_number, u.name as creator_name 
                          FROM {$this->table} si 
                          LEFT JOIN customers c ON si.customer_id = c.id 
                          LEFT JOIN sales_orders so ON si.so_id = so.id 
                          LEFT JOIN users u ON si.created_by = u.id
                          WHERE si.id = :id AND si.company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function getInvoiceItems($invoiceId) {
        $this->db->query("SELECT sii.*, p.sku as product_sku 
                          FROM sales_invoice_items sii 
                          LEFT JOIN products p ON sii.product_id = p.id 
                          WHERE sii.invoice_id = :iid");
        $this->db->bind(':iid', $invoiceId);
        return $this->db->resultSet();
    }

    public function createInvoice($data, $items) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} 
                    (company_id, invoice_number, customer_id, so_id, invoice_date, due_date, subtotal, discount, tax_amount, grand_total, notes, created_by) 
                    VALUES (:cid, :inv_num, :cust_id, :so_id, :inv_date, :due_date, :sub, :disc, :tax, :grand, :notes, :user)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':inv_num', $data['invoice_number']);
            $this->db->bind(':cust_id', $data['customer_id']);
            $this->db->bind(':so_id', !empty($data['so_id']) ? $data['so_id'] : null);
            $this->db->bind(':inv_date', $data['invoice_date']);
            $this->db->bind(':due_date', $data['due_date']);
            $this->db->bind(':sub', $data['subtotal']);
            $this->db->bind(':disc', $data['discount']);
            $this->db->bind(':tax', $data['tax_amount']);
            $this->db->bind(':grand', $data['grand_total']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();
            
            $invoiceId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO sales_invoice_items (invoice_id, product_id, description, quantity, unit_price, discount, tax_rate, subtotal) 
                        VALUES (:iid, :prod_id, :desc, :qty, :price, :disc, :tax, :subtotal)";
            
            $updateStockSql = "UPDATE products SET quantity = quantity - :qty WHERE id = :prod_id";

            foreach ($items as $item) {
                // إدخال تفاصيل الفاتورة
                $this->db->query($sqlItem);
                $this->db->bind(':iid', $invoiceId);
                $this->db->bind(':prod_id', !empty($item['product_id']) ? $item['product_id'] : null);
                $this->db->bind(':desc', $item['description']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':price', $item['unit_price']);
                $this->db->bind(':disc', $item['discount']);
                $this->db->bind(':tax', $item['tax_rate']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();

                // خصم المخزون آلياً للمنتجات الملموسة
                if (!empty($item['product_id'])) {
                    $this->db->query($updateStockSql);
                    $this->db->bind(':qty', $item['quantity']);
                    $this->db->bind(':prod_id', $item['product_id']);
                    $this->db->execute();
                }
            }

            // تحديث رصيد العميل (الذمم المدينة)
            $this->db->query("UPDATE customers SET current_balance = current_balance + :grand WHERE id = :cust_id AND company_id = :cid");
            $this->db->bind(':grand', $data['grand_total']);
            $this->db->bind(':cust_id', $data['customer_id']);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            // تحديث حالة أمر البيع إذا كان مرتبطاً
            if (!empty($data['so_id'])) {
                $this->db->query("UPDATE sales_orders SET status = 'Invoiced' WHERE id = :soid");
                $this->db->bind(':soid', $data['so_id']);
                $this->db->execute();
            }

            // 🟢 إنشاء القيد المحاسبي الآلي للمبيعات 🟢
            $this->createSalesJournalEntry($invoiceId, $data['invoice_number'], $data['grand_total'], $data['tax_amount'], $data['subtotal']);

            $this->db->commit();
            return $invoiceId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    private function createSalesJournalEntry($invoiceId, $invoiceNumber, $grandTotal, $taxAmount, $subtotal) {
        $companyId = Session::get('company_id') ?: 1;
        
        // جلب حسابات الدليل المحاسبي
        $this->db->query("SELECT id FROM accounting_accounts WHERE account_type = 'Asset' AND account_name LIKE '%عملاء%' LIMIT 1");
        $arAcc = $this->db->single();
        
        $this->db->query("SELECT id FROM accounting_accounts WHERE account_type = 'Revenue' LIMIT 1");
        $revAcc = $this->db->single();

        if ($arAcc && $revAcc) {
            require_once '../app/models/JournalEntry.php';
            $jeModel = new JournalEntry();
            
            $data = [
                'journal_number' => 'JV-SI-' . time(),
                'date' => date('Y-m-d'),
                'description' => "مبيعات فاتورة رقم: {$invoiceNumber}",
                'total_amount' => $grandTotal
            ];

            $lines = [
                // مدين: حساب العملاء (AR)
                ['account_id' => $arAcc->id, 'description' => "مستحقات فاتورة {$invoiceNumber}", 'debit' => $grandTotal, 'credit' => 0],
                // دائن: حساب الإيرادات (Revenue)
                ['account_id' => $revAcc->id, 'description' => "إيراد مبيعات فاتورة {$invoiceNumber}", 'debit' => 0, 'credit' => $subtotal - $data['discount'] ?? 0]
            ];

            // إضافة سطر الضريبة (إن وجد)
            if ($taxAmount > 0) {
                $this->db->query("SELECT id FROM accounting_accounts WHERE account_name LIKE '%VAT%' OR account_name LIKE '%ضريبة%' LIMIT 1");
                $taxAcc = $this->db->single();
                if ($taxAcc) {
                    $lines[] = ['account_id' => $taxAcc->id, 'description' => "ضريبة مبيعات فاتورة {$invoiceNumber}", 'debit' => 0, 'credit' => $taxAmount];
                } else {
                    $lines[1]['credit'] += $taxAmount; // دمجه مع الإيراد إذا لم يوجد حساب ضريبة
                }
            }

            $jeModel->createEntry($data, $lines);
        }
    }

    public function deleteInvoice($id) {
        $this->db->beginTransaction();
        try {
            $inv = $this->getInvoiceById($id);
            if ($inv) {
                // عكس رصيد العميل
                $this->db->query("UPDATE customers SET current_balance = current_balance - :grand WHERE id = :cust_id AND company_id = :cid");
                $this->db->bind(':grand', $inv->grand_total);
                $this->db->bind(':cust_id', $inv->customer_id);
                $this->db->bind(':cid', Session::get('company_id') ?: 1);
                $this->db->execute();
                
                // استرجاع المخزون
                $items = $this->getInvoiceItems($id);
                $updateStockSql = "UPDATE products SET quantity = quantity + :qty WHERE id = :prod_id";
                foreach($items as $it) {
                    if(!empty($it->product_id)) {
                        $this->db->query($updateStockSql);
                        $this->db->bind(':qty', $it->quantity);
                        $this->db->bind(':prod_id', $it->product_id);
                        $this->db->execute();
                    }
                }
            }

            $this->db->query("DELETE FROM sales_invoice_items WHERE invoice_id = :id");
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