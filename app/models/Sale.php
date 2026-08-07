<?php
// app/models/Sale.php

class Sale extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'invoices';
    }

    public function getAllInvoices(): array {
        $sql = "SELECT i.*, c.name as customer_name, u.name as sales_rep_name 
                FROM {$this->table} i 
                LEFT JOIN customers c ON i.customer_id = c.id 
                LEFT JOIN users u ON i.sales_rep_id = u.id
                ORDER BY i.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * إصدار فاتورة مبيعات مع خصم المخزون، وإنشاء القيد المحاسبي التلقائي، وتحديث رصيد العميل
     */
    public function createInvoice(array $data, array $items): bool {
        try {
            $this->db->beginTransaction();
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);
            $userId = Session::getUserId();

            // 1. إدراج الفاتورة
            $sqlInvoice = "INSERT INTO invoices (invoice_number, customer_id, customer_name, total_amount, sales_rep_id, created_at) 
                           VALUES (:invoice_number, :customer_id, :customer_name, :total_amount, :sales_rep_id, NOW())";
            $this->db->query($sqlInvoice);
            $this->db->bind(':invoice_number', $invoiceNumber);
            $this->db->bind(':customer_id', $data['customer_id'] ?? null, PDO::PARAM_INT);
            $this->db->bind(':customer_name', $data['customer_name']);
            $this->db->bind(':total_amount', $data['total_amount']);
            $this->db->bind(':sales_rep_id', $userId, PDO::PARAM_INT);
            $this->db->execute();

            $invoiceId = (int)$this->db->lastInsertId();

            // 2. إدراج أصناف الفاتورة وخصم المخزون + التحقق من توفر الكمية
            $sqlItem = "INSERT INTO invoice_items (invoice_id, product_id, quantity, price, subtotal, created_at) 
                        VALUES (:invoice_id, :product_id, :quantity, :price, :subtotal, NOW())";
            $sqlStock = "UPDATE products SET quantity = quantity - :qty WHERE id = :pid AND quantity >= :qty";

            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':invoice_id', $invoiceId, PDO::PARAM_INT);
                $this->db->bind(':product_id', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':quantity', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();

                $this->db->query($sqlStock);
                $this->db->bind(':qty', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':pid', $item['product_id'], PDO::PARAM_INT);
                $this->db->execute();

                if ($this->db->rowCount() === 0) {
                    throw new Exception("الكمية المتاحة غير كافية للمنتج رقم: " . $item['product_id']);
                }

                // فحص تنبيه حد إعادة الطلب
                $this->db->query("SELECT name, quantity, reorder_point FROM products WHERE id = :pid");
                $this->db->bind(':pid', $item['product_id'], PDO::PARAM_INT);
                $prod = $this->db->single();
                if ($prod) {
                    NotificationHelper::checkReorderStock($item['product_id'], $prod->name, $prod->quantity, $prod->reorder_point);
                }
            }

            // 3. تحديث رصيد العميل
            if (!empty($data['customer_id'])) {
                $this->db->query("UPDATE customers SET balance = balance + :amt WHERE id = :cid");
                $this->db->bind(':amt', $data['total_amount']);
                $this->db->bind(':cid', $data['customer_id'], PDO::PARAM_INT);
                $this->db->execute();
            }

            // 4. إنشاء القيد المحاسبي الآلي للمبيعات (Auto Journal Entry)
            // من حـ/ العملاء أو الصندوق (مدين) إلى حـ/ إيرادات المبيعات (دائن)
            $dbCoa = $this->db;
            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'revenue' LIMIT 1");
            $revenueAcc = $dbCoa->single();

            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'asset' LIMIT 1");
            $assetAcc = $dbCoa->single();

            if ($revenueAcc && $assetAcc) {
                $lines = [
                    ['account_id' => $assetAcc->id, 'debit' => $data['total_amount'], 'credit' => 0, 'description' => "فاتورة مبيعات رقم {$invoiceNumber}"],
                    ['account_id' => $revenueAcc->id, 'debit' => 0, 'credit' => $data['total_amount'], 'description' => "إيراد مبيعات فاتورة {$invoiceNumber}"]
                ];

                $accountingModel = new Accounting();
                $accountingModel->createJournalEntry(
                    date('Y-m-d'),
                    "إثبات مبيعات الفاتورة رقم {$invoiceNumber}",
                    'invoice',
                    $invoiceId,
                    $userId,
                    $lines
                );
            }

            // 5. تسجيل النشاط في سجل الأنشطة (Audit Trail)
            ActivityLog::logAction('CREATE', 'Invoices', $invoiceId, "تم إصدار فاتورة مبيعات برقم {$invoiceNumber} بمبلغ {$data['total_amount']}");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getInvoiceById(int $id): ?object {
        $sql = "SELECT i.*, c.name as customer_name, c.phone, c.address, u.name as sales_rep_name 
                FROM invoices i 
                LEFT JOIN customers c ON i.customer_id = c.id 
                LEFT JOIN users u ON i.sales_rep_id = u.id
                WHERE i.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getInvoiceItems(int $invoiceId): array {
        $sql = "SELECT ii.*, p.name as product_name, p.sku 
                FROM invoice_items ii 
                JOIN products p ON ii.product_id = p.id 
                WHERE ii.invoice_id = :invoice_id";
        $this->db->query($sql);
        $this->db->bind(':invoice_id', $invoiceId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * حساب عمولات المبيعات حسب مندوب المبيعات (بنسبة افتراضية 5%)
     */
    public function getSalesCommissions(float $commissionRate = 0.05): array {
        $sql = "SELECT i.sales_rep_id, u.name as rep_name, 
                       COUNT(i.id) as invoice_count, 
                       SUM(i.total_amount) as total_sales,
                       SUM(i.total_amount) * :rate as estimated_commission
                FROM invoices i
                JOIN users u ON i.sales_rep_id = u.id
                GROUP BY i.sales_rep_id, u.name
                ORDER BY total_sales DESC";
        $this->db->query($sql);
        $this->db->bind(':rate', $commissionRate);
        return $this->db->resultSet();
    }
}