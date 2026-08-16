<?php
// Path: app/Core/Queue/Worker.php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Bootstrap\Container;
use App\Core\Contracts\LoggerInterface;
use Throwable;

/**
 * Enterprise Queue Worker
 * الجندي المجهول (Daemon). يقوم بالدوران في حلقة للبحث عن مهام في قاعدة البيانات، 
 * سحبها، فك تشفيرها، وتشغيلها بأمان.
 */
class Worker
{
    protected QueueManager $queueManager;
    protected DeadLetterQueue $dlq;
    protected Container $container;
    protected LoggerInterface $logger;

    /**
     * Worker constructor.
     *
     * @param QueueManager $queueManager
     * @param DeadLetterQueue $dlq
     * @param Container $container
     * @param LoggerInterface $logger
     */
    public function __construct(
        QueueManager $queueManager,
        DeadLetterQueue $dlq,
        Container $container,
        LoggerInterface $logger
    ) {
        $this->queueManager = $queueManager;
        $this->dlq = $dlq;
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * تشغيل عامل الطابور (يُستدعى عادة من سكريبت يعمل عبر الـ Cron Job أو الـ Supervisor).
     *
     * @param string $queue
     * @param int $maxJobs عدد المهام في الدورة الواحدة (0 = مستمر)
     * @return void
     */
    public function work(string $queue = 'default', int $maxJobs = 0): void
    {
        $jobsProcessed = 0;

        while (true) {
            $context = $this->queueManager->pop($queue);

            if (!$context) {
                // لا توجد مهام، نريح السيرفر ثانية واحدة ونكرر البحث
                sleep(1);
                continue;
            }

            $this->processJob($context);

            $jobsProcessed++;
            if ($maxJobs > 0 && $jobsProcessed >= $maxJobs) {
                break; // خروج بعد عدد معين من المهام (مفيد لبيئات الـ Shared Hosting)
            }
        }
    }

    /**
     * معالجة المهمة المسحوبة.
     *
     * @param JobContext $context
     * @return void
     */
    protected function processJob(JobContext $context): void
    {
        try {
            // فك التشفير لتحويل النص إلى كائن Job
            /** @var Job $jobInstance */
            $jobInstance = unserialize($context->payload);

            if (!$jobInstance instanceof Job) {
                throw new \RuntimeException("Payload does not resolve to a valid Job instance.");
            }

            // تنفيذ المهمة وحقن الـ Container بداخلها
            $jobInstance->handle($this->container);

            // نجحت المهمة، نحذفها من الطابور
            $this->queueManager->delete($context->id);
            
            $this->logger->info("Job [ID: {$context->id}] processed successfully.");

        } catch (Throwable $e) {
            $this->handleJobFailure($context, $jobInstance ?? null, $e);
        }
    }

    /**
     * التعامل مع فشل المهمة وتطبيق سياسات إعادة التشغيل.
     *
     * @param JobContext $context
     * @param Job|null $jobInstance
     * @param Throwable $exception
     * @return void
     */
    protected function handleJobFailure(JobContext $context, ?Job $jobInstance, Throwable $exception): void
    {
        $retryPolicy = $jobInstance ? $jobInstance->getRetryPolicy() : new RetryPolicy(1, 0);

        if ($context->attempts >= $retryPolicy->maxAttempts) {
            // فشل نهائي، نقل إلى Dead Letter Queue
            $this->queueManager->delete($context->id);
            $this->dlq->logFailedJob($context->queue, $context->payload, $exception);
            
            $this->logger->error("Job [ID: {$context->id}] failed permanently after {$context->attempts} attempts.", [
                'error' => $exception->getMessage()
            ]);
        } else {
            // إعادة الإصدار للطابور لتجربتها لاحقاً
            $this->queueManager->release($context->id, $retryPolicy->backoffSeconds);
            
            $this->logger->warning("Job [ID: {$context->id}] failed. Retrying later (Attempt {$context->attempts} of {$retryPolicy->maxAttempts}).", [
                'error' => $exception->getMessage()
            ]);
        }
    }
}