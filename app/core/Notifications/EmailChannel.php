<?php
// Path: app/Core/Notifications/EmailChannel.php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Config\MailConfig;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Email Channel
 * يقوم بإرسال الإشعار عبر البريد الإلكتروني (SMTP).
 */
class EmailChannel implements NotificationChannel
{
    protected MailConfig $config;
    protected LoggerInterface $logger;

    public function __construct(MailConfig $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function send(Notification $notification, array $user): bool
    {
        $email = $user['email'] ?? null;

        if (!$email) {
            $this->logger->warning("EmailChannel: User ID {$user['id']} does not have a valid email address.");
            return false;
        }

        // إعداد ترويسات الإيميل القياسية (Enterprise Standards)
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->config->fromName} <{$this->config->fromAddress}>\r\n";
        $headers .= "Reply-To: {$this->config->fromAddress}\r\n";
        $headers .= "X-Mailer: ERP-System/PHP\r\n";

        // تنسيق الـ Body داخل HTML آمن
        $htmlBody = "<html><body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>";
        $htmlBody .= "<h2>{$notification->title}</h2>";
        $htmlBody .= "<p>" . nl2br($notification->body) . "</p>";
        $htmlBody .= "</body></html>";

        // تنفيذ عملية الإرسال (في بيئة الإنتاج يتم استخدام مكتبة مثل PHPMailer أو Symphony Mailer داخلياً، هنا نستخدم Native Mail)
        $success = @mail($email, $notification->title, $htmlBody, $headers);

        if (!$success) {
            $this->logger->error("EmailChannel: Failed to send email to {$email}.");
            return false;
        }

        return true;
    }
}