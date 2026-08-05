<?php
// core/Controller.php

/**
 * المتحكم الأساسي - كل المتحكمات ترث منه
 * يوفر وظائف مشتركة: عرض الصفحات، تحميل النماذج، إعادة التوجيه، وإدارة الجلسة
 */
abstract class Controller {
    
    /**
     * عرض ملف العرض (View)
     */
    protected function view(string $view, array $data = []): void {
        // استخراج المتغيرات لاستخدامها في العرض
        extract($data);
        
        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new \Exception("ملف العرض غير موجود: {$view}");
        }
    }
    
    /**
     * تحميل نموذج (Model)
     */
    protected function model(string $model): object {
        $modelFile = APP_ROOT . '/app/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        
        throw new \Exception("النموذج غير موجود: {$model}");
    }
    
    /**
     * إعادة توجيه إلى رابط محدد
     */
    protected function redirect(string $url): void {
        header("Location: " . URL_ROOT . '/' . ltrim($url, '/'));
        exit;
    }
    
    /**
     * إرجاع استجابة JSON
     */
    protected function jsonResponse(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * التحقق من أن الطلب هو POST
     */
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * التحقق من أن الطلب هو GET
     */
    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * التحقق من أن الطلب هو AJAX
     */
    protected function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * الحصول على بيانات POST منظفة
     */
    protected function getPostData(array $keys = []): array {
        $data = [];
        
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = array_map('trim', $value);
                } else {
                    $data[$key] = trim($value);
                }
            }
        }
        
        // إذا تم تحديد مفاتيح معينة، نرجع فقط هذه المفاتيح
        if (!empty($keys)) {
            $filtered = [];
            foreach ($keys as $key) {
                if (isset($data[$key])) {
                    $filtered[$key] = $data[$key];
                }
            }
            return $filtered;
        }
        
        return $data;
    }
    
    /**
     * الحصول على بيانات GET منظفة
     */
    protected function getQuery(string $key = '', string $default = ''): string {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }
    
    /**
     * الحصول على المعاملات من URL
     */
    protected function getParams(): array {
        $url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        return explode('/', $url);
    }
    
    /**
     * تعيين رسالة مؤقتة (Flash Message)
     */
    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }
    
    /**
     * الحصول على الرسالة المؤقتة وحذفها
     */
    protected function getFlash(): ?array {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
    
    /**
     * التحقق من تسجيل الدخول (مع إعادة توجيه تلقائية)
     * هذه الدالة هي الحل الأساسي لمشكلة طلب تسجيل الدخول المتكرر
     */
    protected function requireAuth(): void {
        // التحقق من وجود جلسة نشطة
        if (!isset($_SESSION['user_id'])) {
            $this->setFlash('warning', 'يرجى تسجيل الدخول أولاً');
            $this->redirect('auth/login');
        }
        
        // التحقق من انتهاء الجلسة (تحديث وقت النشاط)
        $this->checkSessionTimeout();
    }
    
    /**
     * التحقق من انتهاء وقت الجلسة (تلقائياً)
     */
    protected function checkSessionTimeout(): void {
        $lastActivity = $_SESSION['last_activity'] ?? 0;
        $timeout = defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 7200; // افتراضي 2 ساعة
        
        if ($lastActivity > 0 && (time() - $lastActivity) > $timeout) {
            // انتهت الجلسة
            $this->logout();
            $this->setFlash('warning', 'انتهت وقت الجلسة، يرجى تسجيل الدخول مرة أخرى');
            $this->redirect('auth/login');
        }
        
        // تحديث وقت النشاط الحالي
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * تسجيل الخروج (مسح بيانات الجلسة)
     */
    protected function logout(): void {
        // مسح بيانات المستخدم
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        unset($_SESSION['last_activity']);
        unset($_SESSION['login_time']);
        unset($_SESSION['ip_address']);
        unset($_SESSION['user_agent']);
        
        // تدمير الجلسة بالكامل
        session_destroy();
    }
    
    /**
     * التحقق من صلاحية معينة
     */
    protected function requireRole(string $role): void {
        $this->requireAuth();
        
        $userRole = $_SESSION['user_role'] ?? '';
        
        if ($userRole !== $role && $userRole !== 'admin') {
            $this->setFlash('error', 'ليس لديك صلاحية للوصول لهذه الصفحة');
            $this->redirect('dashboard');
        }
    }
    
    /**
     * التحقق من صلاحيات متعددة
     */
    protected function requireAnyRole(array $roles): void {
        $this->requireAuth();
        
        $userRole = $_SESSION['user_role'] ?? '';
        
        if (!in_array($userRole, $roles) && $userRole !== 'admin') {
            $this->setFlash('error', 'ليس لديك صلاحية للوصول لهذه الصفحة');
            $this->redirect('dashboard');
        }
    }
}