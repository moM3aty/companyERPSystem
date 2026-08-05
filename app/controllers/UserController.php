<?php
// app/controllers/UserController.php

class UserController extends Controller {
    
    private User $userModel;

    public function __construct() {
        // حماية صارمة: فقط الإدارة (admin) يحق لها الدخول لهذا المتحكم
        $this->requireRole('admin');
        $this->userModel = $this->model('User');
    }

    public function index(): void {
        $users = $this->userModel->getAllUsers();
        
        $data = [
            'title' => 'المستخدمين والصلاحيات',
            'users' => $users,
            'breadcrumb' => [['label' => 'المستخدمين', 'url' => 'users/index']]
        ];
        
        // نمرر المحتوى إلى كلاس Layout ليتولى تغليفه بالقالب الرئيسي
        ob_start();
        $this->view('users/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'     => trim($_POST['name'] ?? ''),
                'email'    => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role'     => trim($_POST['role'] ?? 'viewer'),
                'phone'    => trim($_POST['phone'] ?? '')
            ];

            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                $this->setFlash('error', 'يرجى تعبئة جميع الحقول المطلوبة.');
                $this->redirect('user/create');
            }

            if ($this->userModel->emailExists($data['email'])) {
                $this->setFlash('error', 'البريد الإلكتروني مسجل مسبقاً لمستخدم آخر.');
                $this->redirect('user/create');
            }

            if ($this->userModel->createUser($data)) {
                $this->setFlash('success', 'تم إنشاء حساب المستخدم بنجاح.');
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
                    ['label' => 'إضافة مستخدم', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('users/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $userId = (int)$id;
            
            // منع المدير من حذف نفسه
            if ($userId === Session::getUserId()) {
                $this->setFlash('error', 'لا يمكنك حذف حسابك الشخصي أثناء تسجيل الدخول به!');
                $this->redirect('user/index');
            }
            
            if ($this->userModel->delete($userId)) {
                $this->setFlash('success', 'تم حذف المستخدم من النظام بنجاح.');
            } else {
                $this->setFlash('error', 'فشل في حذف المستخدم.');
            }
        }
        $this->redirect('user/index');
    }
}