<?php
// core/Security.php

/**
 * فئة الأمان - حماية CSRF والتشفير والتحقق
 */
class Security {
    
    /**
     * توليد رمز CSRF Token
     */
    public static function generateCsrfToken(): string {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * التحقق من صحة رمز CSRF
     */
    public static function verifyCsrfToken(): bool {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * الحصول على حقل CSRF للنموذج
     */
    public static function csrfField(): string {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }
    
    /**
     * تنظيف المدخلات (منع XSS)
     */
    public static function sanitize(string $input): string {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }
    
    /**
     * تنظيف مدخلات مصفوفة
     */
    public static function sanitizeArray(array $data): array {
        return array_map([self::class, 'sanitize'], $data);
    }
    
    /**
     * تشفير النص
     */
    public static function encrypt(string $text): string {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt(
            $text, 
            'AES-256-CBC', 
            ENCRYPTION_KEY, 
            OPENSSL_RAW_DATA, 
            $iv
        );
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * فك تشفير النص
     */
    public static function decrypt(string $encrypted): string {
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, 16);
        $ciphertext = substr($data, 16);
        
        $decrypted = openssl_decrypt(
            $ciphertext, 
            'AES-256-CBC', 
            ENCRYPTION_KEY, 
            OPENSSL_RAW_DATA, 
            $iv
        );
        
        return $decrypted !== false ? $decrypted : '';
    }
    
    /**
     * إنشاء كلمة مرور عشوائية قوية
     */
    public static function generateStrongPassword(int $length = 16): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        $password = '';
        $charLength = strlen($chars) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $charLength)];
        }
        
        // التأكد من وجود حرف كبير وصغير ورقم ورمز خاص
        while (!preg_match('/[A-Z]/', $password) || 
               !preg_match('/[a-z]/', $password) || 
               !preg_match('/[0-9]/', $password) ||
               !preg_match('/[^A-Za-z0-9]/', $password)) {
            $password = self::generateStrongPassword($length);
        }
        
        return $password;
    }
    
    /**
     * التحقق من قوة كلمة المرور
     */
    public static function checkPasswordStrength(string $password): array {
        $score = 0;
        $feedback = [];
        
        // الطول
        if (strlen($password) >= 8) {
            $score += 25;
        } else {
            $feedback[] = 'كلمة المرور قصيرة جداً (8 أحرف على الأقل)';
        }
        
        // أحرف كبيرة وصغيرة
        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            $score += 25;
        } else {
            $feedback[] = 'أضف حروفاً صغيراً وكبيراً';
        }
        
        // أرقام
        if (preg_match('/\d/', $password)) {
            $score += 25;
        } else {
            $feedback[] = 'أضف أرقاماً إلى كلمة المرور';
        }
        
        // رموز خاصة
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score += 25;
        } else {
            $feedback[] = 'أضف رموزاً خاصة مثل !@#$%';
        }
        
        // التقييم
        $level = match(true) {
            $score >= 90 => 'قوية جداً',
            $score >= 75 => 'قوية',
            $score >= 50 => 'متوسطة',
            $score >= 25 => 'ضعيفة',
            default        => 'ضعيفة جداً',
        };
        
        $color = match(true) {
            $score >= 75 => 'success',
            $score >= 50 => 'warning',
            default        => 'danger',
        };
        
        return [
            'score'   => $score,
            'level'   => $level,
            'color'   => $color,
            'feedback'=> $feedback
        ];
    }
    
    /**
     * منع هجمات Brute Force (تأخير كثافة المحاولات)
     */
    public static function isRateLimited(string $action, int $maxAttempts = 5, int $decayMinutes = 15): bool {
        $key = 'rate_limit_' . $action . '_' . md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'time' => 0];
        
        $now = time();
        $decayTime = $decayMinutes * 60;
        
        // إعادة تعيين العداد بعد انتهاء فترة الانتظار
        if ($now - $attempts['time'] > $decayTime) {
            $attempts = ['count' => 0, 'time' => 0];
        }
        
        if ($attempts['count'] >= $maxAttempts) {
            return true; // تم تجاوز الحد المسموح
        }
        
        return false;
    }
    
    /**
     * تسجيل محاولة فاشلة
     */
    public static function logFailedAttempt(string $action): void {
        $key = 'rate_limit_' . $action . '_' . md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['time'] = time();
    }
    
    /**
     * تنظيف المدخلات للبحث
     */
    public static function cleanSearch(string $query): string {
        $query = trim($query);
        $query = preg_replace('/[\'\";<>]/', '', $query);
        $query = preg_replace('/\s+/', ' ', $query);
        return $query;
    }
}
