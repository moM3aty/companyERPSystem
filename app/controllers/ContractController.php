<?php
// app/controllers/ContractController.php

class ContractController extends Controller {
    
    public function __construct() {
        // التحقق من تسجيل الدخول
        $this->requireAuth();
    }

    /**
     * عرض قائمة العقود (التنبيهات، وعرض الكل)
     */
    public function index() {
        $db = Database::getInstance();
        
        $db->query('
            SELECT c.*, 
                   CASE 
                       WHEN c.party_type = "customer" THEN cust.name 
                       WHEN c.party_type = "supplier" THEN sup.name 
                   END as party_name
            FROM contracts c
            LEFT JOIN customers cust ON c.party_id = cust.id AND c.party_type = "customer"
            LEFT JOIN suppliers sup ON c.party_id = sup.id AND c.party_type = "supplier"
            ORDER BY c.end_date ASC
        ');
        $contracts = $db->resultSet();
        
        $data = [
            'title' => 'إدارة العقود',
            'contracts' => $contracts,
            'flash' => $this->getFlash()
        ];
        
        $this->view('contracts/index', $data);
    }

    /**
     * إنشاء عقد جديد
     */
    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'party_type' => $_POST['party_type'] ?? 'customer',
                'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : null,
                'start_date' => $_POST['start_date'] ?? null,
                'end_date' => $_POST['end_date'] ?? null,
                'value' => (float)($_POST['value'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
                'description' => trim($_POST['description'] ?? '')
            ];
            
            $errors = [];
            if (empty($data['title'])) $errors[] = 'عنوان العقد مطلوب';
            if (empty($data['party_id'])) $errors[] = 'يجب تحديد الطرف المعني بالعقد';
            if (empty($data['end_date'])) $errors[] = 'تاريخ انتهاء العقد مطلوب للتنبيهات';

            if (empty($errors)) {
                $db = Database::getInstance();
                $db->query('
                    INSERT INTO contracts (title, party_type, party_id, start_date, end_date, value, status, description)
                    VALUES (:title, :party_type, :party_id, :start_date, :end_date, :value, :status, :description)
                ');
                
                $db->bind(':title', $data['title']);
                $db->bind(':party_type', $data['party_type']);
                $db->bind(':party_id', $data['party_id'], PDO::PARAM_INT);
                $db->bind(':start_date', $data['start_date']);
                $db->bind(':end_date', $data['end_date']);
                $db->bind(':value', $data['value']);
                $db->bind(':status', $data['status']);
                $db->bind(':description', $data['description']);
                
                if ($db->execute()) {
                    $this->setFlash('success', 'تم تسجيل العقد بنجاح وإدراجه في نظام التنبيهات.');
                    $this->redirect('contract/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ العقد في قاعدة البيانات.');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }
            $this->redirect('contract/create');
            
        } else {
            // جلب العملاء والموردين للعرض في القوائم المنسدلة
            $db = Database::getInstance();
            $db->query('SELECT id, name FROM customers ORDER BY name ASC');
            $customers = $db->resultSet();
            
            $db->query('SELECT id, name FROM suppliers ORDER BY name ASC');
            $suppliers = $db->resultSet();

            $data = [
                'title' => 'تسجيل عقد جديد',
                'customers' => $customers,
                'suppliers' => $suppliers,
                'flash' => $this->getFlash()
            ];

            $this->view('contracts/create', $data);
        }
    }

    /**
     * حذف عقد
     */
    public function delete($id) {
        if ($this->isPost()) {
            $db = Database::getInstance();
            $db->query('DELETE FROM contracts WHERE id = :id');
            $db->bind(':id', $id, PDO::PARAM_INT);
            
            if ($db->execute()) {
                $this->setFlash('success', 'تم حذف العقد بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('contract/index');
    }
}