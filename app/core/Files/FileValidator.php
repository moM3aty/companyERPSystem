<?php
// Path: app/Core/Files/FileValidator.php

declare(strict_types=1);

namespace App\Core\Files;

use finfo;
use App\Core\Exceptions\StorageException;

/**
 * Enterprise File Validator
 * يقوم بفحص الملفات المرفوعة أمنياً (الحجم، صيغة الملف الحقيقية وليس الامتداد فقط).
 */
class FileValidator
{
    /**
     * فحص أمان وسلامة الملف المرفوع.
     *
     * @param array $file البيانات القادمة من $_FILES['input_name']
     * @param array $allowedMimes صيغ الملفات المسموح بها (فارغ = صور و PDF)
     * @param int $maxSize الحجم الأقصى بالبايت (0 = 5 ميجا افتراضياً)
     * @return void
     * @throws StorageException
     */
    public function validate(array $file, array $allowedMimes = [], int $maxSize = 0): void
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new StorageException('Invalid upload parameters.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new StorageException($this->getUploadErrorMessage($file['error']));
        }

        $limit = $maxSize > 0 ? $maxSize : 5242880; // Default: 5MB
        if ($file['size'] > $limit) {
            throw new StorageException('File size exceeds the allowed limit of ' . ($limit / 1024 / 1024) . 'MB.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $mimes = empty($allowedMimes) ? ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'] : $allowedMimes;

        if (!in_array($mimeType, $mimes, true)) {
            throw new StorageException('Invalid file format. Uploaded type: ' . $mimeType);
        }
    }

    /**
     * تحويل كود الخطأ إلى رسالة مقروءة.
     *
     * @param int $code
     * @return string
     */
    protected function getUploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            default               => 'Unknown upload error occurred.',
        };
    }
}