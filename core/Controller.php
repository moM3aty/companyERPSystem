<?php
// المسار: core/Controller.php

class Controller {
    // تحميل الموديل
    public function model($model) {
        require_once APP_ROOT . '/app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = []) {
        // استخدام APP_ROOT لضمان المسار المطلق الدقيق
        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            extract($data);
            require $viewFile;
        } else {
            die("الملف غير موجود: " . $viewFile);
        }
    }

    public function getQuery($key, $default = '') {
        return isset($_GET[$key]) ? htmlspecialchars(trim($_GET[$key])) : $default;
    }

    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function redirect($url) {
        header('Location: ' . URLROOT . '/' . $url);
        exit;
    }

    public function setFlash($type, $message) {
        Session::setFlash($type, $message);
    }

    public function getFlash() {
        return Session::getFlash();
    }

    public function requireAuth() {
        if (!Session::isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }

    public function requireRole($role) {
        $this->requireAuth();
        if (!Session::hasRole($role)) {
            $this->setFlash('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
            $this->redirect('dashboard/index');
        }
    }

    public function requireAnyRole(array $roles) {
        $this->requireAuth();
        if (!Session::hasAnyRole($roles)) {
            $this->setFlash('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
            $this->redirect('dashboard/index');
        }
    }
}