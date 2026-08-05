<?php
// core/Session.php

/**
 * فئة إدارة الجلسات - توفر واجهة موحدة للتعامل مع بيانات الجلسة
 */
class Session {
    
    /**
     * تعيين قيمة في الجلسة
     */
    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }
    
    /**
     * الحصول على قيمة من الجلسة
     */
    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * التحقق من وجود قيمة في الجلسة
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    /**
     * حذف قيمة من الجلسة
     */
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }
    
    /**
     * تسجيل دخول المستخدم
     */
    public static function login(int $userId, string $name, string $role, array $extraData = []): void {
        $_SESSION['user_id']   = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // بيانات إضافية
        foreach ($extraData as $key => $value) {
            $_SESSION[$key] = $value;
        }
        
        // تحديث وقت آخر نشاط
        self::set('last_activity', time());
    }
    
    /**
     * تسجيل خروج المستخدم
     */
    public static function logout(): void {
        // حفظ وقت آخر نشاط قبل التسجيل الخروج (اختياري)
        // يمكن إضافة تسجيل في سجل الأنشاطات هنا
        
        // مسح بيانات المستخدم
        $userKeys = ['user_id', 'user_name', 'user_role', 'login_time', 'ip_address', 'user_agent'];
        foreach ($userKeys as $key) {
            self::remove($key);
        }
    }
    
    /**
     * التحقق من تسجيل الدخول
     */
    public static function isLoggedIn(): bool {
        return self::has('user_id');
    }
    
    /**
     * الحصول على معرف المستخدم الحالي
     */
    public static function getUserId(): ?int {
        $id = self::get('user_id');
        return $id ? (int) $id : null;
    }
    
    /**
     * الحصول على اسم المستخدم الحالي
     */
    public static function getUserName(): string {
        return self::get('user_name', 'زائر');
    }
    
    /**
     * الحصول على صلاحية المستخدم الحالي
     */
    public static function getUserRole(): string {
        return self::get('user_role', 'guest');
    }
    
    /**
     * التحقق من صلاحية معينة
     */
    public static function hasRole(string $role): bool {
        $userRole = self::getUserRole();
        return $userRole === $role || $userRole === 'admin';
    }
    
    /**
     * التحقق من صلاحيات متعددة
     */
    public static function hasAnyRole(array $roles): bool {
        return in_array(self::getUserRole(), $roles) || self::hasRole('admin');
    }
    
    /**
     * تعيين رسالة مؤقتة
     */
    public static function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }
    
    /**
     * الحصول على الرسالة المؤقتة وحذفها
     */
    public static function getFlash(): ?array {
        if (self::has('flash')) {
            $flash = $_SESSION['flash'];
            self::remove('flash');
            return $flash;
        }
        return null;
    }
    
    /**
     * التحقق من انتهاء الجلسة (تنشيط تلقائي)
     */
    public static function checkTimeout(): void {
        $lastActivity = self::get('last_activity', 0);
        $timeout = SESSION_LIFETIME;
        
        if ($lastActivity > 0 && (time() - $lastActivity) > $timeout) {
            self::logout();
            self::setFlash('warning', 'انتهت وقت الجلسة، يرجى تسجيل الدخول مرة أخرى');
            header("Location: " . URL_ROOT . '/auth/login');
            exit();
        }
        
        // تحديث وقت النشاط
        self::set('last_activity', time());
    }
    
    /**
     * تدمير البيانات مع الجلسة
     */
    public static function setTempData(string $key, mixed $value, int $minutes = 30): void {
        $_SESSION['temp_' . $key] = [
            'value'     => $value,
            'expires_at' => time() + ($minutes * 60)
        ];
    }
    
    /**
     * استرداد البيانات المؤقتة
     */
    public static function getTempData(string $key): mixed {
        $key = 'temp_' . $key;
        
        if (!isset($_SESSION[$key])) {
            return null;
        }
        
        $data = $_SESSION[$key];
        
        // حذف البيانات منتهية الصلاحية
        if (time() > $data['expires_at']) {
            unset($_SESSION[$key]);
            return null;
        }
        
        return $data['value'];
    }
    
    /**
     * الحصول على اسم العرض المختصر
     */
    public static function getDisplayName(): string {
        $name = self::getUserName();
        return $name ?: 'مستخدم';
    }
    
    /**
     * الحصول على الحروف الأولى من الاسم
     */
    public static function getInitials(): string {
        $name = self::getUserName();
        if (empty($name)) return 'م';
        return mb_substr($name, 0, 2);
    }
    
    /**
     * تدمير قائمة الصفحات التي زارها المستخدم
     */
    public static function addVisitedPage(string $page): void {
        $pages = self::get('visited_pages', []);
        
        if (!in_array($page, $pages)) {
            $pages[] = $page;
            // حفظ آخر 20 صفحة فقط
            $pages = array_slice($pages, -20);
            self::set('visited_pages', $pages);
        }
    }
}

