<?php
// app/controllers/UserController.php

class UserController extends Controller {
    
    private $userModel;

    public function __construct() {
        $this->requireAuth();
        $this->requireRole('admin'); // فقط الإدارة تتحكم في المستخدمين
        $this->userModel = $this->model('User');
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        
        $data = [
            'title' => 'المستخدمين والصلاحيات',
            'users' => $users,
            'breadcrumb' => [
                ['label' => 'الإعدادات', 'url' => '#'],
                ['label' => 'إدارة المستخدمين', 'url' => 'user/index']
            ]
        ];
        
        ob_start();
        $this->view('users/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => trim($_POST['role'] ?? 'viewer'),
                'password' => trim($_POST['password'] ?? '')
            ];

            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الأساسية (الاسم، البريد، وكلمة المرور).');
            } elseif ($this->userModel->emailExists($data['email'])) {
                $this->setFlash('error', 'البريد الإلكتروني مستخدم مسبقاً لحساب آخر.');
            } else {
                if ($this->userModel->createUser($data)) {
                    $this->setFlash('success', 'تم إنشاء حساب المستخدم بنجاح.');
                    $this->redirect('user/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الإنشاء.');
                }
            }
        }

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

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('user/index');
            return;
        }
        
        $userId = (int)$id;
        $user = $this->userModel->getUserById($userId);
        
        if (!$user) {
            $this->setFlash('error', 'المستخدم غير موجود.');
            $this->redirect('user/index');
            return;
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => trim($_POST['role'] ?? 'viewer'),
                'password' => trim($_POST['password'] ?? '')
            ];

            if (empty($data['name']) || empty($data['email'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الأساسية (الاسم والبريد الإلكتروني).');
            } elseif ($this->userModel->emailExists($data['email'], $userId)) {
                $this->setFlash('error', 'البريد الإلكتروني مستخدم مسبقاً لحساب آخر.');
            } else {
                if ($this->userModel->updateUser($userId, $data)) {
                    $this->setFlash('success', 'تم تعديل بيانات وصلاحيات المستخدم بنجاح.');
                    
                    // إذا كان المستخدم يقوم بتعديل حسابه الشخصي، نقوم بتحديث الجلسة (Session)
                    if ($userId === Session::getUserId()) {
                        Session::set('user_name', $data['name']);
                        Session::set('user_role', $data['role']);
                    }
                    
                    $this->redirect('user/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء تعديل البيانات.');
                }
            }
        }

        $data = [
            'title' => 'تعديل الصلاحيات والمستخدم',
            'user' => $user,
            'breadcrumb' => [
                ['label' => 'المستخدمين', 'url' => 'user/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('users/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $userId = (int)$id;
            
            if ($userId === Session::getUserId()) {
                $this->setFlash('error', 'لا يمكنك حذف حسابك الشخصي أثناء استخدامه.');
            } else {
                if ($this->userModel->deleteUser($userId)) {
                    $this->setFlash('success', 'تم حذف المستخدم من النظام بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء محاولة الحذف.');
                }
            }
        }
        $this->redirect('user/index');
    }
}