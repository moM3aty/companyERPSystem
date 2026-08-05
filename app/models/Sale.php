<?php
// app/models/Sale.php

class Sale extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'invoices';
    }

    /**
     * جلب جميع فواتير المبيعات
     */
    public function getAllInvoices(): array {
        $sql = "SELECT i.*, c.name as registered_customer_name 
                FROM invoices i 
                LEFT JOIN customers c ON i.customer_id = c.id 
                ORDER BY i.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * إنشاء فاتورة مبيعات جديدة وخصم المخزون باستخدام Transactions
     */
    public function createInvoice(string $customerName, float $totalAmount, array $items): bool {
        try {
            // 1. بدء المعاملة لضمان سلامة قاعدة البيانات
            $this->db->beginTransaction();

            // 2. توليد رقم فاتورة فريد
            $invoiceNumber = Helpers::generateInvoiceNumber();

            // 3. إدخال الفاتورة الأساسية
            $sqlInvoice = "INSERT INTO invoices (invoice_number, customer_name, total_amount, created_at) 
                           VALUES (:invoice_number, :customer_name, :total_amount, NOW())";
            $this->db->query($sqlInvoice);
            $this->db->bind(':invoice_number', $invoiceNumber);
            $this->db->bind(':customer_name', $customerName);
            $this->db->bind(':total_amount', $totalAmount);
            $this->db->execute();

            $invoiceId = (int)$this->db->lastInsertId();

            // 4. إدخال عناصر الفاتورة وخصم المخزون الفعلي
            $sqlItem = "INSERT INTO invoice_items (invoice_id, product_id, quantity, price, subtotal, created_at) 
                        VALUES (:invoice_id, :product_id, :quantity, :price, :subtotal, NOW())";
            
            // استعلام تحديث المخزون بحيث لا يسمح بأن يكون الرصيد بالسالب
            $sqlStock = "UPDATE products SET quantity = quantity - :qty WHERE id = :pid AND quantity >= :qty";

            foreach ($items as $item) {
                // إدراج الصنف المباع
                $this->db->query($sqlItem);
                $this->db->bind(':invoice_id', $invoiceId, PDO::PARAM_INT);
                $this->db->bind(':product_id', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':quantity', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();

                // خصم الكمية من جدول المنتجات
                $this->db->query($sqlStock);
                $this->db->bind(':qty', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':pid', $item['product_id'], PDO::PARAM_INT);
                $this->db->execute();
                
                // التحقق: إذا لم يتأثر أي صف، فهذا يعني أن المخزون غير كافٍ للصنف
                if ($this->db->rowCount() === 0) {
                    throw new Exception("الكمية غير كافية للمنتج رقم: " . $item['product_id']);
                }
            }

            // 5. تأكيد الحفظ
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            // التراجع عن جميع التغييرات في حال فشل أي خطوة (أو نقص المخزون)
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * جلب الفاتورة برقم المعرف
     */
    public function getInvoiceById(int $id): ?object {
        $sql = "SELECT i.*, c.name as registered_customer_name 
                FROM invoices i 
                LEFT JOIN customers c ON i.customer_id = c.id 
                WHERE i.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * جلب أصناف الفاتورة التفصيلية مع أسماء المنتجات
     */
    public function getInvoiceItems(int $invoiceId): array {
        $sql = "SELECT ii.*, p.name as product_name, p.sku 
                FROM invoice_items ii 
                JOIN products p ON ii.product_id = p.id 
                WHERE ii.invoice_id = :invoice_id";
        $this->db->query($sql);
        $this->db->bind(':invoice_id', $invoiceId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}