<?php
// Path: app/Core/Contracts/FileStorageInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise File Storage Interface
 * عقد يضمن إمكانية استبدال طريقة تخزين الملفات (Local Storage إلى AWS S3 مثلاً) 
 * دون الحاجة لتغيير أي سطر من كود التطبيق الأساسي (Controllers/Services).
 */
interface FileStorageInterface
{
    /**
     * التحقق من وجود الملف.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * جلب محتوى الملف بالكامل.
     *
     * @param string $path
     * @return string|null
     */
    public function get(string $path): ?string;

    /**
     * حفظ محتوى نصي أو Stream في مسار معين.
     *
     * @param string $path مسار الحفظ (مثال: invoices/inv-001.pdf)
     * @param string $contents المحتوى
     * @return bool
     */
    public function put(string $path, string $contents): bool;

    /**
     * حذف ملف من مسار معين.
     *
     * @param string|array $paths
     * @return bool
     */
    public function delete(string|array $paths): bool;

    /**
     * جلب الرابط العام للملف ليتم استخدامه في المتصفح.
     *
     * @param string $path
     * @return string
     */
    public function url(string $path): string;

    /**
     * جلب الحجم الفعلي للملف بالبايت.
     *
     * @param string $path
     * @return int
     */
    public function size(string $path): int;
}