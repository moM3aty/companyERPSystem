<?php
// app/controllers/SettingsController.php

class SettingsController extends Controller {
    
    public function __construct() {
        $this->requireRole('admin');
    }

    public function index() {
        $accountingModel = $this->model('Accounting');
        $userModel = $this->model('User');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['form_action'] ?? '';
            switch ($action) {
                case 'save_company':
                    $this->saveCompanySettings($accountingModel);
                    break;
                case 'save_profile':
                    $this->saveProfile($userModel);
                    break;
                case 'change_password':
                    $this->changePassword($userModel);
                    break;
                default:
                    $this->setFlash('error', 'عملية غير معروفة');
                    $this->redirect('settings/index');
            }
        }

        $settings = [];
        $allRows = $accountingModel->getAllSettings();
        foreach ($allRows as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }

        $settings = array_merge([
            'company_name'  => 'شركتي',
            'company_email' => 'info@company.com',
            'company_phone' => '',
            'vat_number'    => '',
            'currency'      => 'ر.س',
            'tax_rate'      => '15',
            'company_logo'  => ''
        ], $settings);

        $user = $userModel->getUserById(Session::getUserId());

        $systemInfo = [
            'php_version'    => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'db_host'        => DB_HOST,
            'db_name'        => DB_NAME,
            'app_version'    => APP_VERSION,
            'max_upload'     => ini_get('upload_max_filesize'),
            'memory_limit'   => ini_get('memory_limit'),
            'timezone'       => date_default_timezone_get(),
        ];

        $employeeModel = $this->model('Employee');
        $productModel  = $this->model('Product');
        $systemStats = [
            'employees'   => $employeeModel->count(),
            'products'    => $productModel->count(),
            'invoices'    => $accountingModel->getInvoiceCount(),
            'expenses'    => count($accountingModel->getExpenses()),
        ];

        $data = [
            'title'       => 'إعدادات النظام',
            'settings'    => $settings,
            'user'        => $user,
            'system_info' => $systemInfo,
            'system_stats'=> $systemStats,
            'breadcrumb'  => [['label' => 'الإعدادات', 'url' => 'settings/index']]
        ];

        ob_start();
        $this->view('settings/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    private function saveCompanySettings($model) {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $model->updateSetting('company_name', trim($_POST['company_name'] ?? ''));
        $model->updateSetting('company_email', trim($_POST['company_email'] ?? ''));
        $model->updateSetting('company_phone', trim($_POST['company_phone'] ?? ''));
        $model->updateSetting('vat_number', trim($_POST['vat_number'] ?? ''));
        $model->updateSetting('currency', trim($_POST['currency'] ?? 'ر.س'));
        $model->updateSetting('tax_rate', trim($_POST['tax_rate'] ?? '15'));

        // إصلاح مسار رفع الصورة (بدون سلاش في البداية لتسهيل القراءة في الـ View)
       if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
    
    // إنشاء مجلد الرفع إذا لم يكن موجوداً
    $uploadDir = dirname(APP_ROOT) . '/public/uploads/logos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmpPath = $_FILES['company_logo']['tmp_name'];
    $fileName = $_FILES['company_logo']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // تأمين الامتداد
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
    if (in_array($fileExtension, $allowedExts)) {
        
        $newFileName = 'logo_' . Session::get('company_id') . '_' . time() . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // تحديث قيمة الشعار في مصفوفة الإعدادات ليتم حفظها في الداتابيز
            // المسار المحفوظ في الداتابيز سيكون هكذا:
            $settingsData['company_logo'] = 'uploads/logos/' . $newFileName; 
        }
    } else {
        $this->setFlash('error', 'صيغة الصورة غير مدعومة.');
    }
}

        $this->setFlash('success', 'تم حفظ إعدادات الشركة بنجاح');
        $this->redirect('settings/index');
    }

    private function saveProfile($userModel) {
        $name  = trim($_POST['profile_name'] ?? '');
        $email = trim($_POST['profile_email'] ?? '');
        $phone = trim($_POST['profile_phone'] ?? '');
        $errors = [];

        if (empty($name)) $errors[] = 'الاسم مطلوب';
        
        if (empty($errors)) {
            $db = Database::getInstance();
            $db->query('UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id');
            $db->bind(':name', $name);
            $db->bind(':email', $email);
            $db->bind(':phone', $phone);
            $db->bind(':id', Session::getUserId());
            $db->execute();

            Session::set('user_name', $name);
            $this->setFlash('success', 'تم تحديث الملف الشخصي بنجاح');
        } else {
            $this->setFlash('error', implode(' | ', $errors));
        }
        $this->redirect('settings/index');
    }

    private function changePassword($userModel) {
        $current     = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';
        
        $user = $userModel->getUserById(Session::getUserId());
        
        if (!password_verify($current, $user->password)) {
            $this->setFlash('error', 'كلمة المرور الحالية غير صحيحة');
        } elseif (strlen($newPassword) < 6 || $newPassword !== $confirm) {
            $this->setFlash('error', 'كلمة المرور الجديدة غير متطابقة أو قصيرة');
        } else {
            $db = Database::getInstance();
            $db->query('UPDATE users SET password = :pass WHERE id = :id');
            $db->bind(':pass', password_hash($newPassword, PASSWORD_BCRYPT));
            $db->bind(':id', Session::getUserId());
            $db->execute();
            $this->setFlash('success', 'تم تغيير كلمة المرور');
        }
        $this->redirect('settings/index');
    }
}