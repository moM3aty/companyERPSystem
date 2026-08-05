<?php
// app/models/Purchase.php

class Purchase extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'purchase_orders';
    }

    /**
     * جلب جميع أوامر الشراء مع بيانات المورد
     */
    public function getAllOrders(): array {
        $sql = "SELECT po.*, s.name as supplier_name 
                FROM purchase_orders po 
                LEFT JOIN suppliers s ON po.supplier_id = s.id 
                ORDER BY po.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * جلب تفاصيل أمر شراء محدد
     */
    public function getOrderById(int $poId): ?object {
        $sql = "SELECT po.*, s.name as supplier_name, s.email as supplier_email, s.phone as supplier_phone 
                FROM purchase_orders po 
                LEFT JOIN suppliers s ON po.supplier_id = s.id 
                WHERE po.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $poId, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * جلب أصناف أمر الشراء
     */
    public function getOrderItems(int $poId): array {
        $sql = "SELECT poi.*, p.name as product_name, p.sku 
                FROM purchase_order_items poi 
                LEFT JOIN products p ON poi.product_id = p.id 
                WHERE poi.po_id = :po_id";
        $this->db->query($sql);
        $this->db->bind(':po_id', $poId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * إنشاء أمر الشراء وأصنافه (باستخدام Transaction لضمان سلامة البيانات)
     */
    public function createOrder(array $orderData, array $items): bool {
        try {
            $this->db->beginTransaction();

            // 1. توليد رقم أمر الشراء (PO Number)
            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

            // 2. إدخال أمر الشراء الأساسي
            $sqlOrder = "INSERT INTO purchase_orders (po_number, supplier_id, total_amount, status, notes, created_at) 
                         VALUES (:po_number, :supplier_id, :total_amount, :status, :notes, NOW())";
            $this->db->query($sqlOrder);
            $this->db->bind(':po_number', $poNumber);
            $this->db->bind(':supplier_id', $orderData['supplier_id'], PDO::PARAM_INT);
            $this->db->bind(':total_amount', $orderData['total_amount']);
            $this->db->bind(':status', $orderData['status']);
            $this->db->bind(':notes', $orderData['notes']);
            $this->db->execute();

            $poId = $this->db->lastInsertId();

            // 3. إدخال الأصناف المرتبطة
            $sqlItems = "INSERT INTO purchase_order_items (po_id, product_id, quantity_ordered, unit_price, total) 
                         VALUES (:po_id, :product_id, :quantity, :price, :total)";
            foreach ($items as $item) {
                $this->db->query($sqlItems);
                $this->db->bind(':po_id', $poId, PDO::PARAM_INT);
                $this->db->bind(':product_id', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':quantity', $item['quantity_ordered'], PDO::PARAM_INT);
                $this->db->bind(':price', $item['unit_price']);
                $this->db->bind(':total', $item['total']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * استلام كميات بضاعة وتحديث المخزون ورصيد المورد
     */
    public function receiveItems(int $poId, array $receivedItems): bool {
        try {
            $this->db->beginTransaction();
            
            $po = $this->getOrderById($poId);
            if (!$po) throw new Exception("أمر الشراء غير موجود");

            $allItemsFullyReceived = true;
            $totalReceivedCost = 0.0;

            // 1. المرور على العناصر المستلمة لتحديثها
            foreach ($receivedItems as $productId => $data) {
                $qtyReceivedNow = (int)($data['quantity_received'] ?? 0);
                
                if ($qtyReceivedNow > 0) {
                    // جلب بيانات الصنف من أمر الشراء الحالي للتأكد من السعر والكميات
                    $this->db->query("SELECT * FROM purchase_order_items WHERE po_id = :po_id AND product_id = :product_id LIMIT 1");
                    $this->db->bind(':po_id', $poId, PDO::PARAM_INT);
                    $this->db->bind(':product_id', $productId, PDO::PARAM_INT);
                    $item = $this->db->single();

                    if ($item) {
                        // تحديث الكمية المستلمة في تفاصيل أمر الشراء
                        $this->db->query("UPDATE purchase_order_items SET quantity_received = quantity_received + :qty WHERE id = :id");
                        $this->db->bind(':qty', $qtyReceivedNow, PDO::PARAM_INT);
                        $this->db->bind(':id', $item->id, PDO::PARAM_INT);
                        $this->db->execute();

                        // تحديث الكمية في جدول المنتجات الأساسي (المخزون)
                        $this->db->query("UPDATE products SET quantity = quantity + :qty WHERE id = :pid");
                        $this->db->bind(':qty', $qtyReceivedNow, PDO::PARAM_INT);
                        $this->db->bind(':pid', $productId, PDO::PARAM_INT);
                        $this->db->execute();

                        // حساب تكلفة البضاعة المستلمة فعلياً لترحيلها للمورد
                        $totalReceivedCost += ($qtyReceivedNow * (float)$item->unit_price);
                    }
                }
            }

            // 2. التحقق مما إذا كان قد تم استلام كافة الأصناف المطلوبة
            $items = $this->getOrderItems($poId);
            foreach ($items as $itm) {
                if ($itm->quantity_received < $itm->quantity_ordered) {
                    $allItemsFullyReceived = false;
                    break;
                }
            }

            // 3. إضافة قيمة البضاعة المستلمة إلى رصيد المورد كـ دائن (يجب دفعها)
            if ($totalReceivedCost > 0) {
                $this->db->query("UPDATE suppliers SET balance = balance + :cost WHERE id = :sid");
                $this->db->bind(':cost', $totalReceivedCost);
                $this->db->bind(':sid', $po->supplier_id, PDO::PARAM_INT);
                $this->db->execute();
            }

            // 4. تحديث حالة أمر الشراء (تم الاستلام بالكامل أو تم الطلب/استلام جزئي)
            $newStatus = $allItemsFullyReceived ? 'delivered' : 'ordered';
            
            $statusUpdateSql = "UPDATE purchase_orders SET status = :status";
            if ($allItemsFullyReceived) {
                $statusUpdateSql .= ", received_date = CURRENT_DATE()";
            }
            $statusUpdateSql .= " WHERE id = :po_id";
            
            $this->db->query($statusUpdateSql);
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':po_id', $poId, PDO::PARAM_INT);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}