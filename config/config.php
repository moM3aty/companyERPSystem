<?php
//config/config.php
// ============================
// إعدادات قاعدة البيانات
// ============================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'erp_system');

// ============================
// إعدادات التطبيق
// ============================
define('APP_NAME', 'ERP Pro System');
define('APP_ENV', 'development');
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}
define('URL_ROOT', 'http://localhost/companyErpSystem/public');
define('APP_VERSION', '2.0.0');

// ============================
// إعدادات الأمان
// ============================
define('CSRF_TOKEN_NAME', '_token');
define('SESSION_LIFETIME', 7200);

// ============================
// إعدادات التشفير
// ============================
define('ENCRYPTION_KEY', 'your-secret-key-here');

// ============================
// إعدادات الجلسة (الإصلاح الأساسي)
// ============================
if (session_status() === PHP_SESSION_NONE) {
    // تعيين مسار الكوكي ليشمل كل التطبيق
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax'); // يسمح بالانتقال من رابط خارجي
    
    // إذا كنت تستخدم HTTPS، اجعل secure = 1
    ini_set('session.cookie_secure', 0);
    
    // تعيين مجلد مخصص للجلسات (اختياري)
    $sessionDir = APP_ROOT . '/sessions';
    if (!is_dir($sessionDir)) {
        mkdir($sessionDir, 0777, true);
    }
    session_save_path($sessionDir);
    
    // بدء الجلسة
    session_start();
}

// ============================
// عرض الأخطاء حسب البيئة
// ============================
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}