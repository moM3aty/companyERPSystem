<?php
// app/controllers/AuthController.php

class AuthController extends Controller {
    
    public function index(): void {
        $this->redirect('auth/login');
    }

    public function login(): void {
        // إذا كان مسجل الدخول بالفعل، وجهه للوحة التحكم
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($this->isPost()) {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $this->setFlash('error', 'الرجاء إدخال البريد الإلكتروني وكلمة المرور');
                $this->redirect('auth/login');
            }

            $db = Database::getInstance();
            $db->query("SELECT * FROM users WHERE email = :email LIMIT 1");
            $db->bind(':email', $email);
            $user = $db->single();

            // للتبسيط في بيئة التطوير والاختبار (admin / admin)
            // في الإنتاج الفعلي يجب استخدام password_verify($password, $user->password)
            if ($user && ($password === 'admin' || password_verify($password, $user->password))) {
                
                // تسجيل الجلسة
                Session::login($user->id, $user->name, $user->role);
                
                // توجيه للوحة التحكم
                $this->redirect('dashboard');
            } else {
                $this->setFlash('error', 'بيانات الدخول غير صحيحة');
                $this->redirect('auth/login');
            }
        }

        $data = [
            'title' => 'تسجيل الدخول | ERP Pro',
            'flash' => $this->getFlash()
        ];

        $this->view('auth/login', $data);
    }

    public function logout(): void {
        Session::logout();
        $this->redirect('auth/login');
    }
}