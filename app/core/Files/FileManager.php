<?php
// Path: app/Core/Files/FileManager.php

declare(strict_types=1);

namespace App\Core\Files;

use App\Core\Config\Config;
use App\Core\Exceptions\StorageException;

/**
 * Enterprise File Manager
 * يتولى مسؤولية نقل الملفات بعد التحقق منها، وتوليد أسماء آمنة (UUID/Hex) لمنع التصادم والاختراق.
 */
class FileManager
{
    protected FileValidator $validator;
    protected string $storageRoot;

    /**
     * FileManager constructor.
     *
     * @param FileValidator $validator
     * @param Config $config
     */
    public function __construct(FileValidator $validator, Config $config)
    {
        $this->validator = $validator;
        // افتراضياً سيتم حفظ الملفات في مجلد storage/app/public داخل المشروع
        $this->storageRoot = $config->get('app.root') . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';
    }

    /**
     * رفع وحفظ ملف جديد بأمان.
     *
     * @param array $file البيانات من $_FILES
     * @param string $directory المجلد الفرعي (مثال: 'avatars' أو 'invoices')
     * @param array $allowedMimes الأنواع المسموحة
     * @param int $maxSize الحد الأقصى للحجم
     * @return string المسار النسبي للملف المحفوظ (ليتم تخزينه في قاعدة البيانات)
     * @throws StorageException
     */
    public function store(array $file, string $directory = 'uploads', array $allowedMimes = [], int $maxSize = 0): string
    {
        $this->validator->validate($file, $allowedMimes, $maxSize);

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . strtolower($extension);

        $targetDirectory = $this->storageRoot . DIRECTORY_SEPARATOR . trim($directory, '\/');

        if (!is_dir($targetDirectory)) {
            // إنشاء المجلد بصلاحيات 0755 لتوفير الأمان
            if (!mkdir($targetDirectory, 0755, true)) {
                throw new StorageException('Failed to create storage directory.');
            }
        }

        $fullPath = $targetDirectory . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new StorageException('Failed to move the uploaded file to its final destination.');
        }

        // إرجاع المسار النسبي الذي يمكن حفظه في الـ Database واستدعاءه من الـ URL
        return trim($directory, '/') . '/' . $filename;
    }

    /**
     * حذف ملف من السيرفر.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        $fullPath = $this->storageRoot . DIRECTORY_SEPARATOR . trim($path, '\/');

        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}