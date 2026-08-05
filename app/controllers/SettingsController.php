<?php
// app/controllers/SettingsController.php

class SettingsController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit();
        }
        // يتطلب صلاحية admin
        if ($_SESSION['user_role'] !== 'admin') {
            $this->setFlash('error', 'ليس لديك صلاحية للوصول إلى الإعدادات');
            header('Location: ' . URL_ROOT . '/dashboard');
            exit();
        }
    }

    /**
     * رسائل مؤقتة (Flash Messages)
     */
    private function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * الصفحة الرئيسية للإعدادات
     * تتعامل مع 4 أنماط: عرض | حفظ إعدادات الشركة | تحديث الملف الشخصي | تغيير كلمة المرور
     */
    public function index() {
        $accountingModel = $this->model('Accounting');
        $userModel = $this->model('User');

        // ========================================
        // معالجة النماذج المُرسلة
        // ========================================
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
                    header('Location: ' . URL_ROOT . '/settings/index');
                    exit();
            }
        }

        // ========================================
        // جلب البيانات وعرض الصفحة
        // ========================================

        // تحميل الإعدادات كمصفوفة مفتاحية
        $settings = [];
        $allRows = $accountingModel->getAllSettings();
        foreach ($allRows as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }

        // القيم الافتراضية لو مفيش إعدادات محفوظة
        $settings = array_merge([
            'company_name'  => 'شركتي',
            'company_email' => 'info@company.com',
            'company_phone' => '0500000000',
            'currency'      => 'ر.س',
            'tax_rate'      => '15'
        ], $settings);

        // بيانات المستخدم الحالي
        $user = $userModel->getUserById($_SESSION['user_id']);

        // معلومات النظام
        $systemInfo = [
            'php_version'    => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'db_host'        => DB_HOST,
            'db_name'        => DB_NAME,
            'app_version'    => '2.0.0',
            'max_upload'     => ini_get('upload_max_filesize'),
            'memory_limit'   => ini_get('memory_limit'),
            'timezone'       => date_default_timezone_get(),
        ];

        // إحصائيات سريعة للنظام
        $employeeModel = $this->model('Employee');
        $productModel  = $this->model('Product');
        $systemStats = [
            'employees'   => count($employeeModel->getEmployees()),
            'products'    => count($productModel->getProducts()),
            'invoices'    => $accountingModel->getInvoiceCount(),
            'expenses'    => count($accountingModel->getExpenses()),
        ];

        $data = [
            'title'       => 'إعدادات النظام',
            'settings'    => $settings,
            'user'        => $user,
            'system_info' => $systemInfo,
            'system_stats'=> $systemStats,
            'flash'       => $this->getFlash()
        ];

        $this->view('settings/index', $data);
    }

    /**
     * حفظ إعدادات الشركة
     */
    private function saveCompanySettings($model) {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $fields = [
            'company_name'  => ['required' => true,  'label' => 'اسم الشركة'],
            'company_email' => ['required' => false, 'label' => 'البريد الإلكتروني', 'type' => 'email'],
            'company_phone' => ['required' => false, 'label' => 'رقم الهاتف'],
            'currency'      => ['required' => true,  'label' => 'العملة'],
            'tax_rate'      => ['required' => true,  'label' => 'نسبة الضريبة', 'type' => 'number'],
        ];

        $errors = [];

        foreach ($fields as $key => $rules) {
            $value = trim($_POST[$key] ?? '');

            if ($rules['required'] && empty($value)) {
                $errors[] = $rules['label'] . ' مطلوب';
                continue;
            }

            if (!empty($value)) {
                if (isset($rules['type']) && $rules['type'] === 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = 'صيغة ' . $rules['label'] . ' غير صحيحة';
                        continue;
                    }
                }

                if (isset($rules['type']) && $rules['type'] === 'number') {
                    $num = floatval($value);
                    if ($num < 0 || $num > 100) {
                        $errors[] = $rules['label'] . ' يجب أن تكون بين 0 و 100';
                        continue;
                    }
                }
            }

            // لا توجد أخطاء — احفظ القيمة
            if (empty($errors)) {
                $model->updateSetting($key, $value);
            }
        }

        if (empty($errors)) {
            $this->setFlash('success', 'تم حفظ إعدادات الشركة بنجاح');
        } else {
            $this->setFlash('error', implode(' | ', $errors));
        }

        header('Location: ' . URL_ROOT . '/settings/index');
        exit();
    }

    /**
     * تحديث الملف الشخصي للمستخدم الحالي
     */
    private function saveProfile($userModel) {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $name  = trim($_POST['profile_name'] ?? '');
        $email = trim($_POST['profile_email'] ?? '');
        $phone = trim($_POST['profile_phone'] ?? '');
        $errors = [];

        if (empty($name) || mb_strlen($name) < 3) {
            $errors[] = 'الاسم مطلوب (3 أحرف على الأقل)';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'بريد إلكتروني صحيح مطلوب';
        } else {
            // التحقق من عدم تكرار البريد
            $existing = $userModel->findUserByEmail($email);
            if ($existing && $existing->id != $_SESSION['user_id']) {
                $errors[] = 'هذا البريد الإلكتروني مستخدم من قبل مستخدم آخر';
            }
        }

        if (empty($errors)) {
            // استخدام الـ Database مباشرة لأن موديل User لا يملك update
            $db = Database::getInstance();
            $db->query('UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id');
            $db->bind(':name', $name);
            $db->bind(':email', $email);
            $db->bind(':phone', $phone);
            $db->bind(':id', $_SESSION['user_id']);
            $db->execute();

            // تحديث الجلسة
            $_SESSION['user_name'] = $name;

            $this->setFlash('success', 'تم تحديث الملف الشخصي بنجاح');
        } else {
            $this->setFlash('error', implode(' | ', $errors));
        }

        header('Location: ' . URL_ROOT . '/settings/index');
        exit();
    }

    /**
     * تغيير كلمة المرور
     */
    private function changePassword($userModel) {
        $current     = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';
        $errors = [];

        // جلب بيانات المستخدم
        $user = $userModel->getUserById($_SESSION['user_id']);

        if (empty($current) || !password_verify($current, $user->password)) {
            $errors[] = 'كلمة المرور الحالية غير صحيحة';
        }

        if (strlen($newPassword) < 6) {
            $errors[] = 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل';
        }

        if ($newPassword !== $confirm) {
            $errors[] = 'تأكيد كلمة المرور غير متطابق';
        }

        if ($current === $newPassword && !empty($newPassword)) {
            $errors[] = 'كلمة المرور الجديدة يجب أن تختلف عن الحالية';
        }

        if (empty($errors)) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $db = Database::getInstance();
            $db->query('UPDATE users SET password = :pass WHERE id = :id');
            $db->bind(':pass', $hashed);
            $db->bind(':id', $_SESSION['user_id']);
            $db->execute();

            $this->setFlash('success', 'تم تغيير كلمة المرور بنجاح — يُرجى تسجيل الدخول مرة أخرى');
        } else {
            $this->setFlash('error', implode(' | ', $errors));
        }

        header('Location: ' . URL_ROOT . '/settings/index');
        exit();
    }
}