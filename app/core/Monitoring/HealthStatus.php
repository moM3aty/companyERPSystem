<?php
// Path: app/Core/Monitoring/HealthStatus.php

declare(strict_types=1);

namespace App\Core\Monitoring;

use JsonSerializable;

/**
 * Enterprise Health Status DTO
 * يمثل حالة خدمة واحدة داخل النظام (مثال: قاعدة البيانات، الكاش).
 */
class HealthStatus implements JsonSerializable
{
    public const OK = 'ok';
    public const DEGRADED = 'degraded';
    public const FAILED = 'failed';

    public readonly string $serviceName;
    public readonly string $status;
    public readonly ?string $message;
    public readonly ?float $responseTimeMs;

    /**
     * HealthStatus constructor.
     *
     * @param string $serviceName
     * @param string $status
     * @param string|null $message
     * @param float|null $responseTimeMs
     */
    public function __construct(string $serviceName, string $status, ?string $message = null, ?float $responseTimeMs = null)
    {
        $this->serviceName = $serviceName;
        $this->status = $status;
        $this->message = $message;
        $this->responseTimeMs = $responseTimeMs;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'service'       => $this->serviceName,
            'status'        => $this->status,
            'message'       => $this->message,
            'response_time' => $this->responseTimeMs ? round($this->responseTimeMs, 2) . ' ms' : null,
        ];
    }
}