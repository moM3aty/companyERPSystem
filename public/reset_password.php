<?php
// reset_password.php - ضع هذا الملف في مجلد public

// المسار الصحيح لملف الإعدادات
require_once __DIR__ . '/../config/config.php';

// تحميل الكلاسات إذا لزم الأمر (لكننا نحتاج Database)
// إذا لم يتم تحميل Database تلقائياً، نحتاج إلى تضمينها:
if (!class_exists('Database')) {
    require_once __DIR__ . '/../core/Database.php';
}

try {
    $db = Database::getInstance();
    
    $newPassword = 'admin'; // غيرها حسب الرغبة
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $db->query("UPDATE users SET password = :pass WHERE email = 'admin@system.com'");
    $db->bind(':pass', $hashed);
    
    if ($db->execute()) {
        echo "<h3>✅ تم تحديث كلمة المرور بنجاح!</h3>";
        echo "<p><strong>البريد الإلكتروني:</strong> admin@system.com</p>";
        echo "<p><strong>كلمة المرور الجديدة:</strong> " . $newPassword . "</p>";
        echo "<p><strong>الهاش الجديد:</strong> " . $hashed . "</p>";
        echo "<hr>";
        echo "<p>يمكنك الآن <a href='/NourTrust/public/auth/login'>تسجيل الدخول</a> باستخدام البيانات أعلاه.</p>";
    } else {
        echo "❌ حدث خطأ أثناء تحديث كلمة المرور.";
    }
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage();
}