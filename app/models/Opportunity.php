<?php
class Opportunity extends Model {
    protected string $table = 'opportunities';
    
    /**
     * تغيير مرحلة الفرصة
     */
    public function changeStage($id, $newStage, $userId) {
        $this->db->query('
            UPDATE opportunities 
            SET stage = :stage,
                closed_at = CASE WHEN :stage IN ("closed_won","closed_lost") THEN NOW() ELSE NULL END
            WHERE id = :id
        ');
        $this->db->bind(':stage', $newStage);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    
    /**
     * جلب فرص العميل
     */
    public function getByCustomer($customerId) {
        $this->db->query('
            SELECT o.*, u.name as assigned_name
            FROM opportunities o
            LEFT JOIN users u ON o.assigned_to = u.id
            WHERE customer_id = :cid
            ORDER BY id DESC
        ');
        $this->db->bind(':cid', $customerId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}