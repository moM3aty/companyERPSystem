<?php
// Path: app/Core/Reporting/Exporters/CsvExporter.php

declare(strict_types=1);

namespace App\Core\Reporting\Exporters;

use App\Core\Files\FileManager;

/**
 * Enterprise CSV Exporter
 * يقوم بتصدير الـ Generator إلى ملف CSV حقيقي وحفظه باستخدام الـ FileManager.
 */
class CsvExporter
{
    protected FileManager $fileManager;

    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * تصدير البيانات إلى ملف CSV.
     *
     * @param \Generator $data
     * @param array $columns خريطة الأعمدة (مثال: ['invoice_no' => 'Invoice Number'])
     * @param string $filename اسم الملف
     * @return string المسار النهائي للملف المحفوظ
     */
    public function export(\Generator $data, array $columns, string $filename): string
    {
        // استخدام الذاكرة المؤقتة، وإذا تجاوزت 5 ميجا يتم الكتابة على القرص لضمان الاستقرار
        $tempFile = fopen('php://temp/maxmemory:5242880', 'w+');

        // كتابة الترويسة (Headers)
        fputcsv($tempFile, array_values($columns));

        $columnKeys = array_keys($columns);

        // كتابة السجلات صفاً بصف باستخدام הـ Yield
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($columnKeys as $key) {
                $csvRow[] = $row[$key] ?? '';
            }
            fputcsv($tempFile, $csvRow);
        }

        rewind($tempFile);
        $fileContent = stream_get_contents($tempFile);
        fclose($tempFile);

        // حفظ الملف في السيرفر
        // محاكاة ملف مرفوع لتمريره לـ FileManager
        $fakeFile = [
            'name'     => $filename . '.csv',
            'tmp_name' => tempnam(sys_get_temp_dir(), 'csv'),
            'error'    => UPLOAD_ERR_OK,
            'size'     => strlen($fileContent)
        ];
        
        file_put_contents($fakeFile['tmp_name'], $fileContent);

        $path = $this->fileManager->store($fakeFile, 'reports/csv', ['text/csv', 'text/plain']);
        
        unlink($fakeFile['tmp_name']);

        return $path;
    }
}