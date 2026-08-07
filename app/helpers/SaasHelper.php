<?php
// app/helpers/SaasHelper.php

class SaasHelper {
    
    /**
     * التحقق مما إذا كان اشتراك الشركة لا يزال ساري المفعول ولم ينتهِ
     */
    public static function isSubscriptionValid(int $companyId): bool {
        $db = Database::getInstance();
        $db->query("SELECT status, subscription_ends_at FROM companies WHERE id = :id LIMIT 1");
        $db->bind(':id', $companyId, PDO::PARAM_INT);
        $company = $db->single();
        
        if (!$company || $company->status !== 'active') return false;
        
        if (!empty($company->subscription_ends_at)) {
            $endDate = strtotime($company->subscription_ends_at);
            $today = strtotime(date('Y-m-d'));
            if ($today > $endDate) {
                return false; // الاشتراك منتهي
            }
        }
        return true;
    }

    /**
     * التحقق مما إذا كانت باقة الشركة تسمح بإضافة مستخدم جديد
     */
    public static function canAddUser(int $companyId): bool {
        $db = Database::getInstance();
        
        // 1. حساب المستخدمين الحاليين في الشركة
        $db->query("SELECT COUNT(id) as total FROM users WHERE company_id = :cid");
        $db->bind(':cid', $companyId, PDO::PARAM_INT);
        $currentUsers = (int)($db->single()->total ?? 0);
        
        // 2. جلب الحد الأقصى للمستخدمين من باقة الشركة
        $db->query("SELECT p.max_users 
                    FROM companies c 
                    LEFT JOIN saas_packages p ON c.package_id = p.id 
                    WHERE c.id = :cid LIMIT 1");
        $db->bind(':cid', $companyId, PDO::PARAM_INT);
        $maxUsers = (int)($db->single()->max_users ?? 5);
        
        return $currentUsers < $maxUsers;
    }
    
    /**
     * جلب عدد الأيام المتبقية لانتهاء الاشتراك لإظهار تنبيه التجديد
     */
    public static function getExpiryDays(int $companyId): ?int {
        $db = Database::getInstance();
        $db->query("SELECT subscription_ends_at FROM companies WHERE id = :id LIMIT 1");
        $db->bind(':id', $companyId, PDO::PARAM_INT);
        $company = $db->single();
        
        if (!$company || empty($company->subscription_ends_at)) return null; // باقة مفتوحة (مدى الحياة)
        
        $endDate = new DateTime($company->subscription_ends_at);
        $today = new DateTime(date('Y-m-d'));
        
        if ($endDate < $today) return -1; // منتهي
        
        $diff = $today->diff($endDate);
        return (int)$diff->days;
    }
}