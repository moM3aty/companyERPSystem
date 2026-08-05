<?php
class Treasury {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getAllTreasuries() {
        $stmt = $this->db->query("SELECT * FROM treasuries ORDER BY type ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTreasuryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM treasuries WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTransactions($treasury_id) {
        $stmt = $this->db->prepare("SELECT * FROM financial_transactions WHERE treasury_id = ? ORDER BY transaction_date DESC, created_at DESC");
        $stmt->execute([$treasury_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTransaction($data) {
        try {
            $this->db->beginTransaction();

            // 1. تسجيل الحركة المالية
            $stmt = $this->db->prepare("INSERT INTO financial_transactions (treasury_id, transaction_type, amount, transaction_date, reference, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['treasury_id'],
                $data['transaction_type'],
                $data['amount'],
                $data['transaction_date'],
                $data['reference'],
                $data['description'],
                $data['created_by']
            ]);

            // 2. تحديث رصيد الخزنة/البنك
            if ($data['transaction_type'] == 'receipt') {
                $updateStmt = $this->db->prepare("UPDATE treasuries SET current_balance = current_balance + ? WHERE id = ?");
            } else {
                $updateStmt = $this->db->prepare("UPDATE treasuries SET current_balance = current_balance - ? WHERE id = ?");
            }
            $updateStmt->execute([$data['amount'], $data['treasury_id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>