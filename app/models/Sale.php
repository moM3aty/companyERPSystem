<?php
// app/models/Sale.php

use Exception;

class Sale extends Model {

    // جلب جميع الفواتير مع اسم العميل
    public function getInvoices() {
        $this->db->query('
            SELECT i.*, c.name as customer_name 
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            ORDER BY i.id DESC
        ');
        return $this->db->resultSet();
    }

    // جلب فاتورة واحدة بالتفاصيل
    public function getInvoiceById($id) {
        $this->db->query('
            SELECT i.*, c.name as customer_name 
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            WHERE i.id = :id
        ');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    // جلب أصناف فاتورة معينة
    public function getInvoiceItems($invoice_id) {
        $this->db->query('
            SELECT ii.*, p.name as product_name, p.sku 
            FROM invoice_items ii 
            JOIN products p ON ii.product_id = p.id 
            WHERE ii.invoice_id = :inv_id
        ');
        $this->db->bind(':inv_id', $invoice_id, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // إنشاء فاتورة جديدة (مع خصم المخزون)
    public function createInvoice($customerName, $totalAmount, $items) {
        // بدء المعاملة
        $this->db->beginTransaction();
        
        try {
            // 1. إدخال بيانات الفاتورة الرئيسية
            $this->db->query('INSERT INTO invoices (invoice_number, customer_name, total_amount) 
                              VALUES (:inv_num, :cust, :total)');
            $invoiceNumber = 'INV-' . date('YmdHis');
            $this->db->bind(':inv_num', $invoiceNumber);
            $this->db->bind(':cust', $customerName);
            $this->db->bind(':total', $totalAmount);
            $this->db->execute();

            // 2. جلب آخر ID للفاتورة
            $invoiceId = $this->db->lastInsertId();

            // 3. إدخال الأصناف وخصم الكميات من المخزون
            foreach($items as $item) {
                // التحقق من توفر الكمية قبل الخصم
                $this->db->query('SELECT quantity FROM products WHERE id = :prod_id FOR UPDATE');
                $this->db->bind(':prod_id', $item['product_id']);
                $currentQty = $this->db->single();
                
                if(!$currentQty || $currentQty->quantity < $item['quantity']) {
                    throw new Exception('الكمية غير متوفرة للمنتج ID: ' . $item['product_id']);
                }
                
                // إدخال الصنف في الفاتورة
                $this->db->query('INSERT INTO invoice_items (invoice_id, product_id, quantity, price, subtotal) 
                                  VALUES (:inv_id, :prod_id, :qty, :price, :sub)');
                $this->db->bind(':inv_id', $invoiceId);
                $this->db->bind(':prod_id', $item['product_id']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':sub', $item['subtotal']);
                $this->db->execute();

                // خصم الكمية من جدول المنتجات
                $this->db->query('UPDATE products SET quantity = quantity - :qty WHERE id = :prod_id');
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':prod_id', $item['product_id']);
                $this->db->execute();
            }
            
            // تأكيد المعاملة
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            // التراجع عن المعاملة في حالة الخطأ
            $this->db->rollBack();
            error_log('خطأ في إنشاء الفاتورة: ' . $e->getMessage());
            return false;
        }
    }
}