<?php
// app/models/Quote.php

class Quote extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'quotes';
    }

    public function getAllQuotes(): array {
        $sql = "SELECT q.*, c.name as customer_name, u.name as creator_name 
                FROM {$this->table} q 
                LEFT JOIN customers c ON q.customer_id = c.id 
                LEFT JOIN users u ON q.created_by = u.id 
                ORDER BY q.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getQuoteById(int $id): ?object {
        $sql = "SELECT q.*, c.name as customer_name, c.phone, c.address, c.email, u.name as creator_name 
                FROM {$this->table} q 
                LEFT JOIN customers c ON q.customer_id = c.id 
                LEFT JOIN users u ON q.created_by = u.id 
                WHERE q.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getQuoteItems(int $quoteId): array {
        $sql = "SELECT qi.*, p.name as product_name, p.sku 
                FROM quote_items qi 
                LEFT JOIN products p ON qi.product_id = p.id 
                WHERE qi.quote_id = :quote_id";
        $this->db->query($sql);
        $this->db->bind(':quote_id', $quoteId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function createQuote(array $data, array $items): bool {
        try {
            $this->db->beginTransaction();

            $quoteNumber = 'QTE-' . date('Ym') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

            $sqlMain = "INSERT INTO {$this->table} (quote_number, customer_id, total_amount, status, created_by, created_at) 
                        VALUES (:qnum, :cid, :total, 'draft', :uid, NOW())";
            
            $this->db->query($sqlMain);
            $this->db->bind(':qnum', $quoteNumber);
            $this->db->bind(':cid', $data['customer_id'], PDO::PARAM_INT);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':uid', Session::getUserId(), PDO::PARAM_INT);
            $this->db->execute();

            $quoteId = $this->db->lastInsertId();

            // إدراج الأصناف
            $sqlItem = "INSERT INTO quote_items (quote_id, product_id, quantity, unit_price, subtotal) 
                        VALUES (:qid, :pid, :qty, :price, :subtotal)";
            
            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':qid', $quoteId, PDO::PARAM_INT);
                $this->db->bind(':pid', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':qty', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':price', $item['unit_price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    
    public function deleteQuote(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}