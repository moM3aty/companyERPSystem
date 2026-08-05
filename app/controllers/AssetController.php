<?php
// app/controllers/AssetController.php

class AssetController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    /**
     * عرض قائمة الأصول الثابتة
     */
    public function index() {
        $db = Database::getInstance();
        $db->query('
            SELECT a.*, e.name as assigned_to_name
            FROM fixed_assets a
            LEFT JOIN employees e ON a.assigned_to = e.id
            ORDER BY a.id DESC
        ');
        $assets = $db->resultSet();
        
        $data = [
            'title' => 'الأصول الثابتة',
            'assets' => $assets,
            'flash' => $this->getFlash()
        ];
        $this->view('asset/index', $data);
    }

    /**
     * عرض نموذج إضافة أصل جديد
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name']),
                'asset_code' => trim($_POST['asset_code']),
                'category' => trim($_POST['category']),
                'purchase_date' => $_POST['purchase_date'],
                'purchase_price' => (float) $_POST['purchase_price'],
                'salvage_value' => (float) $_POST['salvage_value'],
                'useful_life_years' => (int) $_POST['useful_life_years'],
                'depreciation_method' => $_POST['depreciation_method'],
                'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
                'location' => trim($_POST['location']),
                'notes' => trim($_POST['notes']),
            ];
            
            // التحقق من صحة البيانات
            $errors = [];
            if (empty($data['name'])) $errors[] = 'اسم الأصل مطلوب';
            if (empty($data['asset_code'])) $errors[] = 'كود الأصل مطلوب';
            if (empty($data['purchase_date'])) $errors[] = 'تاريخ الشراء مطلوب';
            if ($data['purchase_price'] <= 0) $errors[] = 'سعر الشراء يجب أن يكون أكبر من صفر';
            
            if (!empty($errors)) {
                $this->setFlash('error', implode(' | ', $errors));
                $this->redirect('asset/create');
            }
            
            // حفظ الأصل
            $db = Database::getInstance();
            $db->query('
                INSERT INTO fixed_assets 
                (name, asset_code, category, purchase_date, purchase_price, salvage_value, 
                 useful_life_years, depreciation_method, current_value, assigned_to, location, notes)
                VALUES 
                (:name, :code, :cat, :pdate, :pprice, :salvage, :life, :method, :cvalue, :assigned, :loc, :notes)
            ');
            $db->bind(':name', $data['name']);
            $db->bind(':code', $data['asset_code']);
            $db->bind(':cat', $data['category']);
            $db->bind(':pdate', $data['purchase_date']);
            $db->bind(':pprice', $data['purchase_price']);
            $db->bind(':salvage', $data['salvage_value']);
            $db->bind(':life', $data['useful_life_years']);
            $db->bind(':method', $data['depreciation_method']);
            $db->bind(':cvalue', $data['purchase_price']); // القيمة الافتتاحية = سعر الشراء
            $db->bind(':assigned', $data['assigned_to'], PDO::PARAM_INT);
            $db->bind(':loc', $data['location']);
            $db->bind(':notes', $data['notes']);
            
            if ($db->execute()) {
                $this->setFlash('success', 'تم إضافة الأصل "' . $data['name'] . '" بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء إضافة الأصل');
            }
            $this->redirect('asset/index');
        } else {
            // جلب الموظفين للاختيار (من يمكن تخصيص الأصل له)
            $employeeModel = $this->model('Employee');
            $employees = $employeeModel->getEmployees();
            
            $data = [
                'title' => 'إضافة أصل جديد',
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];
            $this->view('asset/create', $data);
        }
    }

    /**
     * عرض نموذج تعديل أصل
     */
    public function edit($id) {
        $db = Database::getInstance();
        $db->query('SELECT * FROM fixed_assets WHERE id = :id');
        $db->bind(':id', $id, PDO::PARAM_INT);
        $asset = $db->single();
        
        if (!$asset) {
            $this->setFlash('warning', 'الأصل غير موجود');
            $this->redirect('asset/index');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name']),
                'category' => trim($_POST['category']),
                'purchase_date' => $_POST['purchase_date'],
                'purchase_price' => (float) $_POST['purchase_price'],
                'salvage_value' => (float) $_POST['salvage_value'],
                'useful_life_years' => (int) $_POST['useful_life_years'],
                'depreciation_method' => $_POST['depreciation_method'],
                'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
                'location' => trim($_POST['location']),
                'notes' => trim($_POST['notes']),
            ];
            
            $db->query('
                UPDATE fixed_assets SET
                    name = :name,
                    category = :cat,
                    purchase_date = :pdate,
                    purchase_price = :pprice,
                    salvage_value = :salvage,
                    useful_life_years = :life,
                    depreciation_method = :method,
                    assigned_to = :assigned,
                    location = :loc,
                    notes = :notes
                WHERE id = :id
            ');
            $db->bind(':name', $data['name']);
            $db->bind(':cat', $data['category']);
            $db->bind(':pdate', $data['purchase_date']);
            $db->bind(':pprice', $data['purchase_price']);
            $db->bind(':salvage', $data['salvage_value']);
            $db->bind(':life', $data['useful_life_years']);
            $db->bind(':method', $data['depreciation_method']);
            $db->bind(':assigned', $data['assigned_to'], PDO::PARAM_INT);
            $db->bind(':loc', $data['location']);
            $db->bind(':notes', $data['notes']);
            $db->bind(':id', $id, PDO::PARAM_INT);
            
            if ($db->execute()) {
                $this->setFlash('success', 'تم تحديث الأصل بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التحديث');
            }
            $this->redirect('asset/index');
        } else {
            $employeeModel = $this->model('Employee');
            $employees = $employeeModel->getEmployees();
            
            $data = [
                'title' => 'تعديل أصل',
                'asset' => $asset,
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];
            $this->view('asset/edit', $data);
        }
    }

    /**
     * حذف أصل
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('asset/index');
        }
        
        $db = Database::getInstance();
        $db->query('SELECT name FROM fixed_assets WHERE id = :id');
        $db->bind(':id', $id, PDO::PARAM_INT);
        $asset = $db->single();
        
        if (!$asset) {
            $this->setFlash('warning', 'الأصل غير موجود');
            $this->redirect('asset/index');
        }
        
        $db->query('DELETE FROM fixed_assets WHERE id = :id');
        $db->bind(':id', $id, PDO::PARAM_INT);
        if ($db->execute()) {
            $this->setFlash('success', 'تم حذف الأصل "' . $asset->name . '" بنجاح');
        } else {
            $this->setFlash('error', 'حدث خطأ أثناء الحذف');
        }
        $this->redirect('asset/index');
    }

    /**
     * حساب الإهلاك (يمكن تشغيله عبر Cron Job)
     */
    public function calculateDepreciation() {
        // التحقق من الصلاحية (admin فقط)
        if ($_SESSION['user_role'] !== 'admin') {
            $this->setFlash('error', 'ليس لديك صلاحية');
            $this->redirect('asset/index');
        }
        
        $db = Database::getInstance();
        $db->query('SELECT * FROM fixed_assets WHERE current_value > 0');
        $assets = $db->resultSet();
        
        $updated = 0;
        foreach ($assets as $asset) {
            // حساب الإهلاك السنوي
            $depreciation = 0;
            if ($asset->depreciation_method === 'straight_line') {
                $depreciation = ($asset->purchase_price - $asset->salvage_value) / $asset->useful_life_years;
            } else {
                // طريقة القسط المتناقص (مثال بسيط)
                $rate = 1 / $asset->useful_life_years;
                $depreciation = $asset->current_value * $rate;
            }
            
            $newValue = $asset->current_value - $depreciation;
            if ($newValue < 0) $newValue = 0;
            
            $db->query('UPDATE fixed_assets SET current_value = :value WHERE id = :id');
            $db->bind(':value', $newValue);
            $db->bind(':id', $asset->id, PDO::PARAM_INT);
            if ($db->execute()) {
                $updated++;
            }
        }
        
        $this->setFlash('success', 'تم حساب الإهلاك لـ ' . $updated . ' أصل');
        $this->redirect('asset/index');
    }
}