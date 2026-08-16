<?php
// Path: app/Core/Config/MailConfig.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Mail Configuration
 * إعدادات إرسال البريد الإلكتروني. يدعم إعدادات الـ SMTP القياسية.
 */
class MailConfig
{
    public readonly string $mailer;
    public readonly string $host;
    public readonly int $port;
    public readonly string $username;
    public readonly string $password;
    public readonly string $encryption;
    public readonly string $fromAddress;
    public readonly string $fromName;

    /**
     * MailConfig constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->mailer = $config->get('mail.default', 'smtp');
        $this->host = $config->get('mail.mailers.smtp.host', 'smtp.mailgun.org');
        $this->port = (int) $config->get('mail.mailers.smtp.port', 587);
        $this->username = $config->get('mail.mailers.smtp.username', '');
        $this->password = $config->get('mail.mailers.smtp.password', '');
        $this->encryption = $config->get('mail.mailers.smtp.encryption', 'tls');
        
        $this->fromAddress = $config->get('mail.from.address', 'hello@erp.com');
        $this->fromName = $config->get('mail.from.name', 'ERP System');
    }
}