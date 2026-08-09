<?php
// config/config.php
// ============================
// إعدادات قاعدة البيانات
// ============================

define('DB_HOST', 'localhost');
define('DB_USER', 'u582652079_erpAdmin');
define('DB_PASS', 'UHvw94$k');
define('DB_NAME', 'u582652079_erp');

// ============================
// إعدادات التطبيق
// ============================

define('APP_NAME', 'ERP Pro System');

// Production على Hostinger
define('APP_ENV', 'production');

// مسار المشروع الرئيسي
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ============================
// إعداد الرابط الأساسي
// ============================

// الموقع موجود داخل:
// https://nourtrust.com/ERP/public/

$protocol = (
    isset($_SERVER['HTTPS']) &&
    $_SERVER['HTTPS'] !== 'off'
) ? 'https://' : 'http://';

$serverName = $_SERVER['HTTP_HOST'] ?? 'nourtrust.com';

// اسم مجلد المشروع
$folderName = basename(APP_ROOT);

// الرابط الأساسي للتطبيق
define(
    'URLROOT',
    $protocol . $serverName . '/' . $folderName . '/public'
);

define('APP_VERSION', '2.0.0');

// ============================
// إعدادات الأمان
// ============================

define('CSRF_TOKEN_NAME', '_token');
define('SESSION_LIFETIME', 7200);

// يفضل تغيير هذا المفتاح إلى قيمة عشوائية قوية
define(
    'ENCRYPTION_KEY',
    'CHANGE_THIS_TO_A_LONG_RANDOM_SECRET_KEY_2026'
);

// ============================
// إعدادات الجلسة
// ============================

if (session_status() === PHP_SESSION_NONE) {

    // مدة الجلسة
    ini_set(
        'session.gc_maxlifetime',
        SESSION_LIFETIME
    );

    // الكوكيز تعمل على كامل الموقع
    ini_set(
        'session.cookie_path',
        '/'
    );

    // حماية الكوكي
    ini_set(
        'session.cookie_httponly',
        '1'
    );

    // منع بعض أنواع Session Fixation
    ini_set(
        'session.use_strict_mode',
        '1'
    );

    // مناسب للتنقل داخل نفس الموقع
    ini_set(
        'session.cookie_samesite',
        'Lax'
    );

    // الموقع يعمل HTTPS
    ini_set(
        'session.cookie_secure',
        '1'
    );

    // مجلد الجلسات
    $sessionDir = APP_ROOT . '/sessions';

    if (!is_dir($sessionDir)) {
        mkdir($sessionDir, 0755, true);
    }

    // استخدام مجلد الجلسات الخاص بالمشروع
    if (is_dir($sessionDir) && is_writable($sessionDir)) {
        session_save_path($sessionDir);
    }

    // بدء Session
    session_start();
}

// ============================
// إعداد الأخطاء
// ============================

// Production: لا تعرض أخطاء PHP للمستخدم
if (APP_ENV === 'development') {

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

} else {

    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
