<?php
// app/controllers/UserController.php

class UserController extends Controller {
    
    private User $userModel;

    public function __construct() {
        // حماية: الإدارة فقط من يمكنها إدارة المستخدمين
        $this->requireAnyRole(['super_admin', 'admin', 'manager']);
        $this->userModel = $this->model('User');
    }

    public function index(): void {
        $companyId = Session::get('company_id') ? (int)Session::get('company_id') : null;
        $users = $this->userModel->getUsersByCompany($companyId);
        
        $data = [
            'title' => 'إدارة المستخدمين والصلاحيات',
            'users' => $users,
            'breadcrumb' => [
                ['label' => 'الإدارة والدعم', 'url' => '#'],
                ['label' => 'المستخدمين', 'url' => 'user/index']
            ]
        ];
        
        ob_start();
        $this->view('users/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        // 🔴 التحقق من باقة الـ SaaS قبل السماح بالإضافة 🔴
        require_once APP_ROOT . '/app/helpers/SaasHelper.php';
        $companyId = Session::get('company_id');
        if ($companyId && !SaasHelper::canAddUser((int)$companyId)) {
            $this->setFlash('error', 'لقد تجاوزت الحد الأقصى للمستخدمين المسموح به في باقتك الحالية. يرجى الترقية!');
            $this->redirect('user/index');
            return; // إيقاف التنفيذ فوراً
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'     => trim($_POST['name'] ?? ''),
                'email'    => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'role'     => trim($_POST['role'] ?? 'viewer'),
                'company_id' => $companyId ? (int)$companyId : null
            ];

            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                $this->setFlash('error', 'يرجى تعبئة جميع الحقول الإلزامية.');
                $this->redirect('user/create');
                return;
            }

            // التحقق من عدم تكرار الإيميل
            $db = Database::getInstance();
            $db->query("SELECT id FROM users WHERE email = :email");
            $db->bind(':email', $data['email']);
            if ($db->single()) {
                $this->setFlash('error', 'البريد الإلكتروني مستخدم مسبقاً.');
                $this->redirect('user/create');
                return;
            }

            // تشفير كلمة المرور
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

            // الإضافة لقاعدة البيانات
            $sql = "INSERT INTO users (company_id, name, email, password, role) VALUES (:cid, :name, :email, :password, :role)";
            $db->query($sql);
            $db->bind(':cid', $data['company_id']); // تم إزالة PDO::PARAM_INT ليقبل القيمة null بأمان
            $db->bind(':name', $data['name']);
            $db->bind(':email', $data['email']);
            $db->bind(':password', $data['password']);
            $db->bind(':role', $data['role']);
            
            if ($db->execute()) {
                ActivityLog::logAction('CREATE', 'Users', $db->lastInsertId(), "إضافة مستخدم جديد للنظام: {$data['name']}");
                $this->setFlash('success', 'تم إضافة المستخدم بنجاح.');
                $this->redirect('user/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ المستخدم.');
                $this->redirect('user/create');
            }
        } else {
            $data = [
                'title' => 'إضافة مستخدم جديد',
                'breadcrumb' => [
                    ['label' => 'المستخدمين', 'url' => 'user/index'],
                    ['label' => 'إضافة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('users/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $userId = (int)$id;
            
            // منع المستخدم من حذف نفسه
            if ($userId === Session::getUserId()) {
                $this->setFlash('error', 'لا يمكنك حذف حسابك الشخصي أثناء تسجيل الدخول.');
                $this->redirect('user/index');
                return;
            }

            $db = Database::getInstance();
            $companyId = Session::get('company_id');
            
            if ($companyId) {
                $db->query("DELETE FROM users WHERE id = :id AND company_id = :cid");
                $db->bind(':id', $userId, PDO::PARAM_INT);
                $db->bind(':cid', $companyId, PDO::PARAM_INT);
            } else {
                // إذا كان المالك (Super Admin) يمكنه حذف أي مستخدم عدا نفسه
                $db->query("DELETE FROM users WHERE id = :id AND role != 'super_admin'");
                $db->bind(':id', $userId, PDO::PARAM_INT);
            }
            
            if ($db->execute()) {
                ActivityLog::logAction('DELETE', 'Users', $userId, "تم حذف مستخدم من النظام");
                $this->setFlash('success', 'تم حذف المستخدم بنجاح.');
            } else {
                $this->setFlash('error', 'فشل في حذف المستخدم.');
            }
        }
        $this->redirect('user/index');
    }
}