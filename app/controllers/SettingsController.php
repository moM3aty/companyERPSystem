<?php
// app/controllers/SettingsController.php

class SettingsController extends Controller {
    
    public function __construct() {
        // حماية الوصول
        $this->requireAnyRole(['admin', 'super_admin', 'manager']);
    }

    public function index() {
        // إذا كان الموديل Setting الذي أنشأناه للتو موجوداً، نستخدمه، وإلا نستخدم Accounting القديم
        $settingModel = file_exists('../app/models/Setting.php') ? $this->model('Setting') : $this->model('Accounting');
        $userModel = $this->model('User');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['form_action'] ?? '';
            switch ($action) {
                case 'save_company':
                    $this->saveCompanySettings($settingModel);
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

        // جلب الإعدادات (مع مراعاة التوافقية مع الموديل الجديد والقديم)
        $settings = [];
        if (method_exists($settingModel, 'getAllSettingsArray')) {
            $settings = $settingModel->getAllSettingsArray();
        } elseif (method_exists($settingModel, 'getAllSettings')) {
            $allRows = $settingModel->getAllSettings();
            if (is_array($allRows)) {
                // إذا كانت المصفوفة مسترجعة مباشرة من الموديل الجديد
                if (isset($allRows['company_name'])) {
                    $settings = $allRows;
                } else {
                    foreach ($allRows as $row) {
                        $settings[$row->setting_key] = $row->setting_value;
                    }
                }
            }
        }

        $settings = array_merge([
            'company_name'  => 'شركة نور',
            'company_email' => 'info@company.com',
            'company_phone' => '',
            'vat_number'    => '',
            'commercial_registration' => '',
            'company_address' => '',
            'currency'      => 'SAR',
            'tax_rate'      => '15',
            'fiscal_year_start' => date('Y-01-01'),
            'fiscal_year_end' => date('Y-12-31'),
            'accounting_basis' => 'Accrual',
            'company_logo'  => ''
        ], $settings);

        $user = $userModel->getUserById(Session::getUserId());

        $systemInfo = [
            'php_version'    => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'db_host'        => DB_HOST,
            'db_name'        => DB_NAME,
            'app_version'    => APP_VERSION ?? '1.0',
            'max_upload'     => ini_get('upload_max_filesize'),
            'memory_limit'   => ini_get('memory_limit'),
            'timezone'       => date_default_timezone_get(),
        ];

        // System Stats
        $employeeModel = file_exists('../app/models/Employee.php') ? $this->model('Employee') : null;
        $productModel  = file_exists('../app/models/Product.php') ? $this->model('Product') : null;
        $db = Database::getInstance();
        
        $db->query("SELECT COUNT(*) as c FROM sales_invoices");
        $invCount = $db->single()->c ?? 0;
        
        $db->query("SELECT COUNT(*) as c FROM expenses");
        $expCount = $db->single()->c ?? 0;

        $systemStats = [
            'employees'   => $employeeModel ? $employeeModel->count() : 0,
            'products'    => $productModel ? $productModel->count() : 0,
            'invoices'    => $invCount,
            'expenses'    => $expCount,
        ];

        $data = [
            'title'       => 'إعدادات النظام والشركة',
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

        $settingsData = [
            'company_name'  => trim($_POST['company_name'] ?? ''),
            'company_email' => trim($_POST['company_email'] ?? ''),
            'company_phone' => trim($_POST['company_phone'] ?? ''),
            'vat_number'    => trim($_POST['vat_number'] ?? ''),
            'commercial_registration' => trim($_POST['commercial_registration'] ?? ''),
            'company_address' => trim($_POST['company_address'] ?? ''),
            'currency'      => trim($_POST['currency'] ?? 'SAR'),
            'tax_rate'      => trim($_POST['tax_rate'] ?? '15'),
            'fiscal_year_start' => trim($_POST['fiscal_year_start'] ?? date('Y-01-01')),
            'fiscal_year_end' => trim($_POST['fiscal_year_end'] ?? date('Y-12-31')),
            'accounting_basis' => trim($_POST['accounting_basis'] ?? 'Accrual')
        ];

        // رفع الشعار (Logo)
        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(APP_ROOT) . '/public/uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileTmpPath = $_FILES['company_logo']['tmp_name'];
            $fileName = $_FILES['company_logo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
            if (in_array($fileExtension, $allowedExts)) {
                $newFileName = 'logo_' . (Session::get('company_id') ?: 1) . '_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $settingsData['company_logo'] = 'uploads/logos/' . $newFileName; 
                }
            } else {
                $this->setFlash('error', 'صيغة الصورة غير مدعومة.');
            }
        }

        // الحفظ بناءً على نوع الموديل المتاح
        if (method_exists($model, 'saveSettings')) {
            // الموديل الجديد
            $model->saveSettings($settingsData);
        } elseif (method_exists($model, 'updateSetting')) {
            // الموديل القديم
            foreach ($settingsData as $key => $val) {
                $model->updateSetting($key, $val);
            }
        }

        $this->setFlash('success', 'تم حفظ إعدادات الشركة والنظام المالي بنجاح');
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