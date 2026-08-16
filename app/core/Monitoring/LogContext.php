<?php
// Path: app/Core/Monitoring/LogContext.php

declare(strict_types=1);

namespace App\Core\Monitoring;

/**
 * Enterprise Log Context DTO
 * يقوم بتجميع بيانات البيئة الحالية (من هو المستخدم؟ ما هي الشركة؟ ما هو الـ IP؟)
 * لتضمينها تلقائياً مع كل رسالة Log لسهولة تتبع المشاكل (Traceability).
 */
class LogContext
{
    public readonly ?int $userId;
    public readonly ?int $companyId;
    public readonly string $ipAddress;
    public readonly string $userAgent;
    public readonly string $requestId;

    /**
     * LogContext constructor.
     *
     * @param int|null $userId
     * @param int|null $companyId
     * @param string $ipAddress
     * @param string $userAgent
     * @param string $requestId
     */
    public function __construct(
        ?int $userId = null,
        ?int $companyId = null,
        string $ipAddress = '127.0.0.1',
        string $userAgent = 'Unknown',
        string $requestId = ''
    ) {
        $this->userId = $userId;
        $this->companyId = $companyId;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        // إنشاء معرّف فريد للطلب إذا لم يتم تمريره، لتتبع جميع الأخطاء الناتجة عن نفس الطلب.
        $this->requestId = $requestId ?: bin2hex(random_bytes(8));
    }

    /**
     * تحويل السياق إلى مصفوفة لدمجه مع رسالة الـ Log.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'request_id' => $this->requestId,
            'company_id' => $this->companyId ?? 'System',
            'user_id'    => $this->userId ?? 'Guest',
            'ip'         => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ];
    }
}