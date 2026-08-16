<?php
// Path: app/Core/Workflow/WorkflowAction.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Events\EventBus;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Workflow Action Executor
 * ينفذ المهام الجانبية (Side Effects) عند دخول أو خروج خطوة معينة (مثال: إرسال إيميل).
 */
class WorkflowAction
{
    protected EventBus $eventBus;
    protected LoggerInterface $logger;

    public function __construct(EventBus $eventBus, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->logger = $logger;
    }

    /**
     * تنفيذ أكشن معرف مسبقاً في الداتابيز.
     *
     * @param string $actionType (مثال: 'send_email', 'update_status', 'trigger_event')
     * @param array $parameters متغيرات التنفيذ المخزنة كـ JSON
     * @param WorkflowContext $context
     * @return void
     */
    public function execute(string $actionType, array $parameters, WorkflowContext $context): void
    {
        $this->logger->info("Executing Workflow Action: [{$actionType}] for Instance [{$context->instanceId}]");

        try {
            switch ($actionType) {
                case 'update_status':
                    // المنطق الفعلي لتحديث حالة المستند في قاعدة البيانات سيتم إدارته عبر Event أو Service
                    break;
                case 'send_email':
                    // توجيه أمر لخدمة الإيميلات عبر EventBus
                    break;
                case 'trigger_event':
                    // إطلاق حدث ديناميكي للمنظومة
                    break;
                default:
                    $this->logger->warning("Workflow Action [{$actionType}] is not implemented.");
                    break;
            }
        } catch (\Throwable $e) {
            $this->logger->error("Failed to execute workflow action [{$actionType}]: " . $e->getMessage());
            // لا نوقف سير العمل بالكامل بسبب فشل إرسال إيميل مثلاً
        }
    }
}