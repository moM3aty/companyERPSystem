<?php
// المسار: app/models/PurchaseRequest.php

class PurchaseRequest extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'purchase_requests';
    }

    public function getAllRequests(): array {
        $sql = "SELECT pr.*, u.name as requested_by_name, a.name as approved_by_name 
                FROM {$this->table} pr 
                LEFT JOIN users u ON pr.requested_by = u.id 
                LEFT JOIN users a ON pr.approved_by = a.id 
                ORDER BY pr.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getRequestById(int $id): ?object {
        $sql = "SELECT pr.*, u.name as requested_by_name, a.name as approved_by_name 
                FROM {$this->table} pr 
                LEFT JOIN users u ON pr.requested_by = u.id 
                LEFT JOIN users a ON pr.approved_by = a.id 
                WHERE pr.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getRequestItems(int $requestId): array {
        $sql = "SELECT pri.*, p.name as product_name, p.sku 
                FROM purchase_request_items pri 
                JOIN products p ON pri.product_id = p.id 
                WHERE pri.request_id = :request_id";
        $this->db->query($sql);
        $this->db->bind(':request_id', $requestId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function createPurchaseRequest(array $data, array $items): bool {
        try {
            $this->db->beginTransaction();

            $requestNumber = 'PRQ-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

            $sqlMain = "INSERT INTO {$this->table} (request_number, requested_by, request_date, status, notes, created_at) 
                        VALUES (:request_number, :requested_by, :request_date, 'pending', :notes, NOW())";
            
            $this->db->query($sqlMain);
            $this->db->bind(':request_number', $requestNumber);
            $this->db->bind(':requested_by', Session::getUserId(), PDO::PARAM_INT);
            $this->db->bind(':request_date', $data['request_date']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->execute();

            $requestId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO purchase_request_items (request_id, product_id, quantity, estimated_price) 
                        VALUES (:request_id, :product_id, :quantity, :estimated_price)";
            
            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':request_id', $requestId, PDO::PARAM_INT);
                $this->db->bind(':product_id', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':quantity', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':estimated_price', $item['estimated_price']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateStatus(int $id, string $status, int $adminId): bool {
        $sql = "UPDATE {$this->table} 
                SET status = :status, approved_by = :admin_id, approved_at = NOW() 
                WHERE id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':admin_id', $adminId, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}