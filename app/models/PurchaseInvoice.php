<?php
// app/models/PurchaseInvoice.php

class PurchaseInvoice extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'purchase_invoices';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // Main Purchase Invoices Table
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'      => "INT DEFAULT 1",
            'invoice_number'  => "VARCHAR(50) NOT NULL", // Your internal number
            'supplier_invoice_no' => "VARCHAR(100) NULL", // Supplier's invoice number
            'supplier_id'     => "INT NOT NULL",
            'po_id'           => "INT NULL", // Purchase Order Reference
            'grn_id'          => "INT NULL", // Goods Received Note Reference
            'invoice_date'    => "DATE NOT NULL",
            'due_date'        => "DATE NOT NULL",
            'subtotal'        => "DECIMAL(15,2) DEFAULT 0.00",
            'discount'        => "DECIMAL(15,2) DEFAULT 0.00",
            'tax_amount'      => "DECIMAL(15,2) DEFAULT 0.00",
            'grand_total'     => "DECIMAL(15,2) DEFAULT 0.00",
            'amount_paid'     => "DECIMAL(15,2) DEFAULT 0.00",
            'payment_status'  => "VARCHAR(50) DEFAULT 'Unpaid'", // Unpaid, Partial, Paid
            'match_status'    => "VARCHAR(50) DEFAULT 'Pending'", // Pending, Matched, Exception (3-way match)
            'notes'           => "TEXT NULL",
            'attachment'      => "VARCHAR(255) NULL",
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

        // Purchase Invoice Items Table
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `purchase_invoice_items` (
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
        $sql = "SELECT pi.*, s.company_name as supplier_name, po.po_number, grn.grn_number 
                FROM {$this->table} pi 
                LEFT JOIN suppliers s ON pi.supplier_id = s.id 
                LEFT JOIN purchase_orders po ON pi.po_id = po.id
                LEFT JOIN goods_received_notes grn ON pi.grn_id = grn.id
                WHERE pi.company_id = :cid ORDER BY pi.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getInvoiceById($id) {
        $this->db->query("SELECT pi.*, s.company_name as supplier_name, s.vat_number as supplier_vat, s.address as supplier_address, po.po_number, grn.grn_number, u.name as creator_name 
                          FROM {$this->table} pi 
                          LEFT JOIN suppliers s ON pi.supplier_id = s.id 
                          LEFT JOIN purchase_orders po ON pi.po_id = po.id
                          LEFT JOIN goods_received_notes grn ON pi.grn_id = grn.id
                          LEFT JOIN users u ON pi.created_by = u.id
                          WHERE pi.id = :id AND pi.company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function getInvoiceItems($invoiceId) {
        $this->db->query("SELECT pii.*, p.sku as product_sku 
                          FROM purchase_invoice_items pii 
                          LEFT JOIN products p ON pii.product_id = p.id 
                          WHERE pii.invoice_id = :iid");
        $this->db->bind(':iid', $invoiceId);
        return $this->db->resultSet();
    }

    // 🟢 The core of the 3-Way Match process 🟢
    public function createInvoice($data, $items) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} 
                    (company_id, invoice_number, supplier_invoice_no, supplier_id, po_id, grn_id, invoice_date, due_date, 
                     subtotal, discount, tax_amount, grand_total, match_status, notes, attachment, created_by) 
                    VALUES (:cid, :inv_num, :supp_inv, :supp_id, :po_id, :grn_id, :inv_date, :due_date, 
                            :sub, :disc, :tax, :grand, :match, :notes, :attach, :created_by)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':inv_num', $data['invoice_number']);
            $this->db->bind(':supp_inv', $data['supplier_invoice_no']);
            $this->db->bind(':supp_id', $data['supplier_id']);
            $this->db->bind(':po_id', !empty($data['po_id']) ? $data['po_id'] : null);
            $this->db->bind(':grn_id', !empty($data['grn_id']) ? $data['grn_id'] : null);
            $this->db->bind(':inv_date', $data['invoice_date']);
            $this->db->bind(':due_date', $data['due_date']);
            $this->db->bind(':sub', $data['subtotal']);
            $this->db->bind(':disc', $data['discount']);
            $this->db->bind(':tax', $data['tax_amount']);
            $this->db->bind(':grand', $data['grand_total']);
            $this->db->bind(':match', $data['match_status'] ?? 'Pending');
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':attach', $data['attachment']);
            $this->db->bind(':created_by', Session::getUserId());
            $this->db->execute();
            
            $invoiceId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO purchase_invoice_items (invoice_id, product_id, description, quantity, unit_price, discount, tax_rate, subtotal) 
                        VALUES (:iid, :prod_id, :desc, :qty, :price, :disc, :tax, :subtotal)";
            foreach ($items as $item) {
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
            }

            // Update Supplier Balance (Accounts Payable)
            $this->db->query("UPDATE suppliers SET current_balance = current_balance + :grand WHERE id = :supp_id AND company_id = :cid");
            $this->db->bind(':grand', $data['grand_total']);
            $this->db->bind(':supp_id', $data['supplier_id']);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            // Update GRN Status if linked
            if (!empty($data['grn_id'])) {
                $this->db->query("UPDATE goods_received_notes SET status = 'Invoiced' WHERE id = :grnid");
                $this->db->bind(':grnid', $data['grn_id']);
                $this->db->execute();
            }

            // Generate Automatic Journal Entry (Accounting)
            $this->createAutomaticJournalEntry($invoiceId, $data['invoice_number'], $data['grand_total'], $data['tax_amount']);

            $this->db->commit();
            return $invoiceId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    private function createAutomaticJournalEntry($invoiceId, $invoiceNumber, $grandTotal, $taxAmount) {
        $companyId = Session::get('company_id') ?: 1;
        $userId = Session::getUserId();
        
        $subtotal = $grandTotal - $taxAmount;

        // Try to find default accounts
        $this->db->query("SELECT id FROM chart_of_accounts WHERE type = 'Liability' LIMIT 1"); // Accounts Payable
        $apAcc = $this->db->single();
        
        $this->db->query("SELECT id FROM chart_of_accounts WHERE type = 'Expense' LIMIT 1"); // Purchases/Inventory Expense
        $expAcc = $this->db->single();

        if ($apAcc && $expAcc) {
            require_once '../app/models/JournalEntry.php';
            $jeModel = new JournalEntry();
            
            $data = [
                'journal_number' => 'JV-PI-' . time(),
                'date' => date('Y-m-d'),
                'description' => "فاتورة مورد رقم: {$invoiceNumber}",
                'total_amount' => $grandTotal
            ];

            $lines = [
                // Debit Expense
                ['account_id' => $expAcc->id, 'description' => "مشتريات فاتورة {$invoiceNumber}", 'debit' => $subtotal, 'credit' => 0],
                // Credit Accounts Payable
                ['account_id' => $apAcc->id, 'description' => "مستحقات مورد فاتورة {$invoiceNumber}", 'debit' => 0, 'credit' => $grandTotal]
            ];

            // Add VAT Line if applicable
            if ($taxAmount > 0) {
                $this->db->query("SELECT id FROM chart_of_accounts WHERE account_name LIKE '%VAT%' OR account_name LIKE '%ضريبة%' LIMIT 1");
                $taxAcc = $this->db->single();
                if ($taxAcc) {
                    $lines[] = ['account_id' => $taxAcc->id, 'description' => "ضريبة مشتريات فاتورة {$invoiceNumber}", 'debit' => $taxAmount, 'credit' => 0];
                } else {
                    $lines[0]['debit'] += $taxAmount; // Add to expense if no tax account found
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
                // Reverse Supplier Balance
                $this->db->query("UPDATE suppliers SET current_balance = current_balance - :grand WHERE id = :supp_id AND company_id = :cid");
                $this->db->bind(':grand', $inv->grand_total);
                $this->db->bind(':supp_id', $inv->supplier_id);
                $this->db->bind(':cid', Session::get('company_id') ?: 1);
                $this->db->execute();
            }

            $this->db->query("DELETE FROM purchase_invoice_items WHERE invoice_id = :id");
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