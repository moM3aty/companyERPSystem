<?php
// app/helpers/NotificationHelper.php

/**
 * كلاس مساعد لإدارة وتوجيه الإشعارات والتنبيهات لجميع موديولات النظام
 * يدعم الإشعارات الداخلية (In-App)، الإيميل، ومهيأ للربط مع SMS و WhatsApp APIs
 */
class NotificationHelper {

    /**
     * إرسال إشعارات داخل النظام لمستخدم أو لجميع المدراء
     */
    public static function notify(int $userId, string $type, string $title, string $message, ?string $link = null): bool {
        $notificationModel = new Notification();
        return $notificationModel->send($userId, $type, $title, $message, $link);
    }

    /**
     * إرسال إشعار لجميع المستخدمين الذين يحملون صلاحية محددة (مثل: admin أو manager)
     */
    public static function notifyRole(string $role, string $type, string $title, string $message, ?string $link = null): int {
        $db = Database::getInstance();
        $db->query("SELECT id FROM users WHERE role = :role OR role = 'admin'");
        $db->bind(':role', $role);
        $users = $db->resultSet();

        $count = 0;
        $notificationModel = new Notification();
        foreach ($users as $user) {
            if ($notificationModel->send($user->id, $type, $title, $message, $link)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * تنبيه حد إعادة الطلب للمخزون (Reorder Point Alert)
     */
    public static function checkReorderStock(int $productId, string $productName, int $currentQty, int $reorderPoint): void {
        if ($currentQty <= $reorderPoint) {
            $title = "تنبيه مخزون منخفض: {$productName}";
            $message = "الكمية المتبقية للمنتج ({$productName}) هي {$currentQty}، وتجاوزت حد إعادة الطلب الموصى به ({$reorderPoint}).";
            $link = "product/index";

            self::notifyRole('manager', 'stock_alert', $title, $message, $link);
        }
    }

    /**
     * تنبيه قرب انتهاء العقود (Contract Expiry Alert)
     */
    public static function checkExpiringContracts(): void {
        $db = Database::getInstance();
        $db->query("SELECT * FROM contracts WHERE status = 'active' AND end_date BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)");
        $contracts = $db->resultSet();

        foreach ($contracts as $c) {
            $title = "تنبيه قرب انتهاء عقد: {$c->title}";
            $message = "العقد رقم {$c->contract_number} سينتهي في تاريخ {$c->end_date}. يرجى اتخاذ إجراء التجديد.";
            $link = "contract/index";

            self::notifyRole('manager', 'contract_alert', $title, $message, $link);
        }
    }

    /**
     * تنبيه طلب جديد بانتظار الاعتماد (إجازة، سلفة، طلب شراء)
     */
    public static function notifyPendingApproval(string $moduleName, string $itemRef, string $requestedByName, ?string $link = null): void {
        $title = "طلب جديد يطلب اعتمادك ({$moduleName})";
        $message = "قام الموظف {$requestedByName} برفع طلب ({$itemRef}) وهو بانتظار مراجعتك واعتمادك.";

        self::notifyRole('manager', 'approval_required', $title, $message, $link);
    }
}