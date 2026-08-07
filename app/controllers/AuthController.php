<?php
// app/controllers/AuthController.php

class AuthController extends Controller {

    private User $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function login(): void {
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $this->setFlash('error', 'الرجاء إدخال البريد الإلكتروني وكلمة المرور.');
                $this->view('auth/login', ['email' => $email]);
                return;
            }

            $loggedInUser = $this->userModel->login($email, $password);

            if ($loggedInUser) {
                // التحقق من صلاحية الاشتراك ونظام الـ SaaS باستخدام المساعد
                require_once APP_ROOT . '/app/helpers/SaasHelper.php';
                if ($loggedInUser->role !== 'super_admin' && !SaasHelper::isSubscriptionValid((int)$loggedInUser->company_id)) {
                    $this->setFlash('error', 'عفواً، اشتراك المؤسسة منتهي الصلاحية أو تم إيقافه. يرجى التواصل مع الإدارة للتجديد.');
                    $this->redirect('auth/login');
                    return;
                }

                // حفظ بيانات المستخدم والشركة (Multi-Tenant) في السيشن
                Session::set('user_id', $loggedInUser->id);
                Session::set('user_name', $loggedInUser->name);
                Session::set('user_email', $loggedInUser->email);
                Session::set('user_role', $loggedInUser->role);
                
                Session::set('company_id', $loggedInUser->company_id);
                if(isset($loggedInUser->company_name)) {
                    Session::set('company_name', $loggedInUser->company_name);
                }

                // تسجيل حركة الدخول في سجل الأنشطة
                ActivityLog::logAction('LOGIN', 'Auth', $loggedInUser->id, "تسجيل دخول للنظام (شركة: {$loggedInUser->company_id})");

                $this->redirect('dashboard/index');
            } else {
                $this->setFlash('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة.');
                $this->view('auth/login', ['email' => $email]);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function logout(): void {
        // 1. تسجيل الحدث قبل مسح الجلسة (لأننا نحتاج رقم المستخدم)
        if (Session::isLoggedIn()) {
            ActivityLog::logAction('LOGOUT', 'Auth', Session::getUserId(), "تسجيل خروج من النظام");
        }
        
        // 2. حل المشكلة: استخدام الدوال الأساسية لـ PHP لمسح الجلسة بأمان تام
        session_unset();
        session_destroy();
        
        // 3. التوجيه لصفحة الدخول
        $this->redirect('auth/login');
    }
}