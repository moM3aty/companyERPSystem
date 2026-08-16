<?php
// Path: app/Core/Workflow/WorkflowContext.php

declare(strict_types=1);

namespace App\Core\Workflow;

/**
 * Enterprise Workflow Context
 * يحمل حالة وبيانات النسخة الحالية (Instance) لكي تستخدمها الشروط (Conditions) 
 * والأفعال (Actions) لاتخاذ القرارات (مثل تقييم هل مبلغ الفاتورة أكبر من 1000).
 */
class WorkflowContext
{
    public readonly int $instanceId;
    public readonly int $entityId;
    public readonly string $entityType;
    protected array $payload;

    /**
     * WorkflowContext constructor.
     *
     * @param int $instanceId معرف سير العمل النشط
     * @param int $entityId معرف المستند المرتبط (رقم الفاتورة، رقم طلب الشراء)
     * @param string $entityType نوع المستند
     * @param array $payload البيانات المتغيرة للمستند (المبلغ، القسم، الخ)
     */
    public function __construct(int $instanceId, int $entityId, string $entityType, array $payload = [])
    {
        $this->instanceId = $instanceId;
        $this->entityId = $entityId;
        $this->entityType = $entityType;
        $this->payload = $payload;
    }

    /**
     * جلب قيمة معينة من البيانات لتقييمها.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->payload[$key] ?? $default;
    }

    /**
     * تحديث البيانات في السياق.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->payload[$key] = $value;
    }

    /**
     * جلب كل البيانات.
     *
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}