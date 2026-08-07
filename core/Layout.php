<?php
// المسار: core/Layout.php

class Layout {
    public static function render(string $viewContent, array $data = []) {
        extract($data);
        
        // استخدام APP_ROOT لضمان المسار المطلق الدقيق
        $layoutFile = APP_ROOT . '/app/views/layouts/main.php';
        
        if (file_exists($layoutFile)) {
            require_once $layoutFile;
        } else {
            // إيقاف النظام وإظهار الخطأ بدلاً من طباعة صفحة بيضاء
            die("
                <div style='padding:20px; font-family:tahoma; text-align:right; direction:rtl; background:#fee2e2; color:#dc2626; border:1px solid #f87171;'>
                    <b>خطأ حرج:</b> لم يتم العثور على القالب الرئيسي في المسار:<br>
                    <code style='direction:ltr; display:block; margin-top:10px;'>$layoutFile</code><br>
                    يرجى التأكد من إنشاء مجلد باسم <b>layouts</b> (بحرف s) وبداخله ملف <b>main.php</b>.
                </div>
            ");
        }
    }
}