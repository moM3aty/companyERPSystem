<?php
// core/Controller.php

class Controller {
    
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = []) {
        if (file_exists('../app/views/' . $view . '.php')) {
            require '../app/views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }

    public function redirect($url) {
        header('Location: ' . URLROOT . '/' . $url);
        exit();
    }

    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function requireAuth() {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'يرجى تسجيل الدخول أولاً.');
            $this->redirect('auth/login');
        }
    }

    // 🟢 التعديل الجذري: إعطاء الـ super_admin صلاحية مطلقة للدخول لأي شاشة 🟢
    public function requireRole($role) {
        $currentRole = Session::getUserRole();
        
        // مالك النظام يتخطى جميع الحواجز
        if ($currentRole === 'super_admin') {
            return; 
        }
        
        if ($currentRole !== $role) {
            Session::setFlash('error', 'عفواً، ليس لديك صلاحية كافية (مطلوب: ' . $role . ').');
            $this->redirect('dashboard/index');
        }
    }

    // 🟢 التعديل الجذري: إعطاء الـ super_admin صلاحية مطلقة للدخول لأي شاشة 🟢
    public function requireAnyRole(array $roles) {
        $currentRole = Session::getUserRole();
        
        // مالك النظام يتخطى جميع الحواجز
        if ($currentRole === 'super_admin') {
            return; 
        }

        if (!in_array($currentRole, $roles)) {
            Session::setFlash('error', 'عفواً، ليس لديك صلاحية كافية للوصول لهذه الصفحة.');
            $this->redirect('dashboard/index');
        }
    }

    public function setFlash($type, $message) {
        Session::setFlash($type, $message);
    }
    
    public function getFlash() {
        return Session::getFlash();
    }
}