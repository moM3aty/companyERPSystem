<?php
// app/models/Purchase.php

class Purchase extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'purchase_orders';
    }

    public function getAllOrders(): array {
        $sql = "SELECT po.*, s.name as supplier_name 
                FROM {$this->table} po 
                LEFT JOIN suppliers s ON po.supplier_id = s.id 
                WHERE po.company_id = :cid 
                ORDER BY po.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getOrderById(int $poId): ?object {
        $sql = "SELECT po.*, s.name as supplier_name, s.email as supplier_email, s.phone as supplier_phone 
                FROM {$this->table} po 
                LEFT JOIN suppliers s ON po.supplier_id = s.id 
                WHERE po.id = :id AND po.company_id = :cid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $poId, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getOrderItems(int $poId): array {
        // نتحقق أولاً من أن الأمر يخص الشركة
        if (!$this->getOrderById($poId)) return [];

        $sql = "SELECT poi.*, p.name as product_name, p.sku, p.track_batches 
                FROM purchase_order_items poi 
                LEFT JOIN products p ON poi.product_id = p.id 
                WHERE poi.po_id = :po_id";
        $this->db->query($sql);
        $this->db->bind(':po_id', $poId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function createOrder(array $orderData, array $items): bool {
        try {
            $this->db->beginTransaction();

            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);
            $companyId = Session::get('company_id');

            $sqlOrder = "INSERT INTO {$this->table} (company_id, po_number, supplier_id, total_amount, status, notes, created_at) 
                         VALUES (:cid, :po_number, :supplier_id, :total_amount, :status, :notes, NOW())";
            $this->db->query($sqlOrder);
            $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
            $this->db->bind(':po_number', $poNumber);
            $this->db->bind(':supplier_id', $orderData['supplier_id'], PDO::PARAM_INT);
            $this->db->bind(':total_amount', $orderData['total_amount']);
            $this->db->bind(':status', $orderData['status']);
            $this->db->bind(':notes', $orderData['notes']);
            $this->db->execute();

            $poId = $this->db->lastInsertId();

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

            ActivityLog::logAction('CREATE', 'Purchases', $poId, "تم إنشاء أمر شراء جديد برقم: {$poNumber}");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateOrder(int $poId, array $orderData, array $items): bool {
        try {
            $this->db->beginTransaction();

            $sqlOrder = "UPDATE {$this->table} 
                         SET supplier_id = :supplier_id, total_amount = :total_amount, notes = :notes 
                         WHERE id = :id AND company_id = :cid AND status = 'pending'";
            $this->db->query($sqlOrder);
            $this->db->bind(':supplier_id', $orderData['supplier_id'], PDO::PARAM_INT);
            $this->db->bind(':total_amount', $orderData['total_amount']);
            $this->db->bind(':notes', $orderData['notes']);
            $this->db->bind(':id', $poId, PDO::PARAM_INT);
            $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
            $this->db->execute();

            $this->db->query("DELETE FROM purchase_order_items WHERE po_id = :po_id");
            $this->db->bind(':po_id', $poId, PDO::PARAM_INT);
            $this->db->execute();

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

    public function deleteOrder(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid AND status IN ('pending', 'cancelled')");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function receiveItems(int $poId, array $receivedItems): bool {
        try {
            $this->db->beginTransaction();
            $companyId = Session::get('company_id');
            
            $po = $this->getOrderById($poId);
            if (!$po) throw new Exception("أمر الشراء غير موجود أو لا يتبع لهذه الشركة.");

            $allItemsFullyReceived = true;
            $totalReceivedCost = 0.0;

            foreach ($receivedItems as $productId => $data) {
                $qtyReceivedNow = (int)($data['quantity_received'] ?? 0);

                if ($qtyReceivedNow > 0) {
                    $this->db->query("SELECT * FROM purchase_order_items WHERE po_id = :po_id AND product_id = :product_id LIMIT 1");
                    $this->db->bind(':po_id', $poId, PDO::PARAM_INT);
                    $this->db->bind(':product_id', $productId, PDO::PARAM_INT);
                    $item = $this->db->single();

                    if ($item) {
                        $this->db->query("UPDATE purchase_order_items SET quantity_received = quantity_received + :qty WHERE id = :id");
                        $this->db->bind(':qty', $qtyReceivedNow, PDO::PARAM_INT);
                        $this->db->bind(':id', $item->id, PDO::PARAM_INT);
                        $this->db->execute();

                        $this->db->query("UPDATE products SET quantity = quantity + :qty WHERE id = :pid AND company_id = :cid");
                        $this->db->bind(':qty', $qtyReceivedNow, PDO::PARAM_INT);
                        $this->db->bind(':pid', $productId, PDO::PARAM_INT);
                        $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
                        $this->db->execute();

                        $totalReceivedCost += ($qtyReceivedNow * (float)$item->unit_price);
                    }
                }
            }

            $itemsCheck = $this->getOrderItems($poId);
            foreach ($itemsCheck as $itm) {
                if ($itm->quantity_received < $itm->quantity_ordered) {
                    $allItemsFullyReceived = false; break;
                }
            }

            if ($totalReceivedCost > 0) {
                $this->db->query("UPDATE suppliers SET balance = balance + :cost WHERE id = :sid AND company_id = :cid");
                $this->db->bind(':cost', $totalReceivedCost);
                $this->db->bind(':sid', $po->supplier_id, PDO::PARAM_INT);
                $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
                $this->db->execute();
            }

            $newStatus = $allItemsFullyReceived ? 'delivered' : 'ordered';
            $statusUpdateSql = "UPDATE {$this->table} SET status = :status";
            if ($allItemsFullyReceived) $statusUpdateSql .= ", received_date = CURRENT_DATE()";
            $statusUpdateSql .= " WHERE id = :po_id AND company_id = :cid";
            
            $this->db->query($statusUpdateSql);
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':po_id', $poId, PDO::PARAM_INT);
            $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
            $this->db->execute();

            // الربط المحاسبي (إنشاء قيد)
            $dbCoa = $this->db;
            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'asset' AND name LIKE '%مخزون%' AND company_id = :cid LIMIT 1");
            $dbCoa->bind(':cid', $companyId, PDO::PARAM_INT);
            $inventoryAcc = $dbCoa->single();
            
            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'liability' AND name LIKE '%موردين%' AND company_id = :cid LIMIT 1");
            $dbCoa->bind(':cid', $companyId, PDO::PARAM_INT);
            $payableAcc = $dbCoa->single();

            if ($inventoryAcc && $payableAcc && $totalReceivedCost > 0) {
                $lines = [
                    ['account_id' => $inventoryAcc->id, 'debit' => $totalReceivedCost, 'credit' => 0, 'description' => "استلام مخزون أمر شراء {$po->po_number}"],
                    ['account_id' => $payableAcc->id, 'debit' => 0, 'credit' => $totalReceivedCost, 'description' => "التزامات مستحقة للمورد {$po->supplier_name}"]
                ];
                
                $accountingModel = new Accounting();
                $accountingModel->createJournalEntry(date('Y-m-d'), "إثبات استلام مخزون أمر الشراء {$po->po_number}", 'purchase_order', $poId, Session::getUserId(), $lines);
            }

            ActivityLog::logAction('UPDATE', 'Purchases', $poId, "تم استلام بضاعة لأمر الشراء {$po->po_number} بقيمة {$totalReceivedCost}");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}