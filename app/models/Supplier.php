<?php
// app/models/Supplier.php

class Supplier extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'suppliers';
    }

    /**
     * جلب الموردين مع الفلترة والبحث
     * 
     * @param string $search نص البحث (اسم أو هاتف)
     * @param string $filter نوع المورد (all, individual, company)
     * @return array
     */
    public function getFilteredSuppliers(string $search = '', string $filter = 'all'): array {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE :search OR phone LIKE :search OR contact_person LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if ($filter !== 'all' && in_array($filter, ['individual', 'company'])) {
            $sql .= " AND type = :type";
            $params[':type'] = $filter;
        }

        $sql .= " ORDER BY created_at DESC";

        $this->db->query($sql);
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->resultSet();
    }

    /**
     * حساب إجمالي المستحقات (الديون) لجميع الموردين
     * 
     * @return float
     */
    public function getTotalPayables(): float {
        $this->db->query("SELECT SUM(balance) as total FROM {$this->table} WHERE balance > 0");
        $result = $this->db->single();
        return $result ? (float)$result->total : 0.0;
    }

    /**
     * جلب أوامر الشراء الخاصة بمورد معين
     * 
     * @param int $supplierId
     * @return array
     */
    public function getPurchaseOrders(int $supplierId): array {
        $this->db->query("SELECT * FROM purchase_orders WHERE supplier_id = :supplier_id ORDER BY created_at DESC");
        $this->db->bind(':supplier_id', $supplierId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * جلب الدفعات السابقة التي تمت لهذا المورد (عبر أوامر الشراء)
     * 
     * @param int $supplierId
     * @return array
     */
    public function getPayments(int $supplierId): array {
        // نجلب الدفعات المرتبطة بأوامر شراء تخص هذا المورد
        $sql = "SELECT p.* 
                FROM payments p
                INNER JOIN purchase_orders po ON p.reference_id = po.id
                WHERE p.reference_type = 'purchase_order' 
                AND po.supplier_id = :supplier_id
                ORDER BY p.created_at DESC";
                
        $this->db->query($sql);
        $this->db->bind(':supplier_id', $supplierId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * جلب إحصائيات مالية لمورد محدد (إجمالي مشتريات، إجمالي مدفوع، متبقي)
     * 
     * @param int $supplierId
     * @return array
     */
    public function getSupplierStats(int $supplierId): array {
        // إجمالي المشتريات (أوامر الشراء المكتملة أو المعتمدة)
        $this->db->query("SELECT SUM(total_amount) as total_po FROM purchase_orders WHERE supplier_id = :sid AND status NOT IN ('cancelled', 'rejected')");
        $this->db->bind(':sid', $supplierId, PDO::PARAM_INT);
        $poResult = $this->db->single();
        $totalPayables = $poResult ? (float)$poResult->total_po : 0.0;

        // إجمالي المدفوع
        $payments = $this->getPayments($supplierId);
        $totalPaid = 0.0;
        foreach ($payments as $pay) {
            $totalPaid += (float)$pay->amount;
        }

        // الحصول على الرصيد المسجل في جدول المورد نفسه
        $supplier = $this->findById($supplierId);
        $outstanding = $supplier ? (float)$supplier->balance : 0.0;

        return [
            'totalPayables' => $totalPayables,
            'totalPaid'     => $totalPaid,
            'outstanding'   => $outstanding
        ];
    }
}