<?php
// Path: app/Core/Queue/QueueInterface.php

declare(strict_types=1);

namespace App\Core\Queue;

/**
 * Enterprise Queue Interface
 * العقد الذي يحدد كيفية التعامل مع محرك الطوابير (سواء كان Database أو Redis).
 */
interface QueueInterface
{
    /**
     * دفع مهمة جديدة إلى الطابور.
     *
     * @param string $queue اسم الطابور (مثال: emails, reports)
     * @param string $payload البيانات المشفرة للمهمة (Serialized Object)
     * @param int $delay تأخير التنفيذ بالثواني
     * @return int معرف المهمة
     */
    public function push(string $queue, string $payload, int $delay = 0): int;

    /**
     * سحب مهمة من الطابور لتنفيذها مع عمل (Lock) عليها لمنع التكرار.
     *
     * @param string $queue
     * @return JobContext|null
     */
    public function pop(string $queue): ?JobContext;

    /**
     * حذف المهمة من الطابور بعد نجاح تنفيذها.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * إعادة المهمة للطابور في حالة الفشل (لتجربتها لاحقاً).
     *
     * @param int $id
     * @param int $delay
     * @return void
     */
    public function release(int $id, int $delay = 0): void;
}