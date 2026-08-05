<?php
// ============================
// تعريف المسار الجذري للتطبيق
// ============================
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ============================
// تحميل ملف الإعدادات (يبدأ الجلسة)
// ============================
require_once APP_ROOT . '/config/config.php';

// ============================
// تسجيل الـ autoloader
// ============================
spl_autoload_register(function ($class) {
    $paths = [
        APP_ROOT . '/core/',
        APP_ROOT . '/app/models/',
        APP_ROOT . '/app/controllers/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ============================
// توجيه الطلب
// ============================
$url = trim($_GET['url'] ?? 'dashboard');
$url = filter_var($url, FILTER_SANITIZE_URL);
$parts = explode('/', $url);

$controllerName = ucfirst($parts[0] ?? 'Dashboard') . 'Controller';
$methodName = $parts[1] ?? 'index';
$params = array_slice($parts, 2);

$controllerFile = APP_ROOT . '/app/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    // إذا لم يُعثر المتحكم، وجهه لصفحة الدخول
    $controllerName = 'AuthController';
    $methodName = 'login';
    $params = [];
    $controllerFile = APP_ROOT . '/app/controllers/' . $controllerName . '.php';
    
    if (!file_exists($controllerFile)) {
        http_response_code(404);
        die('الصفحة المطلوبة غير موجودة');
    }
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    http_response_code(404);
    die('المتحكم غير موجود');
}

if (!method_exists($controllerName, $methodName)) {
    $methodName = 'index';
}

$controller = new $controllerName();
call_user_func_array([$controller, $methodName], $params);