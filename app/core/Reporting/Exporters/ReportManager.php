<?php
// Path: app/Core/Reporting/ReportManager.php

declare(strict_types=1);

namespace App\Core\Reporting;

use App\Core\Reporting\Exporters\CsvExporter;
use App\Core\Reporting\Exporters\ExcelExporter;
use App\Core\Reporting\Exporters\PdfExporter;
use App\Core\Exceptions\BusinessException;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;

/**
 * Enterprise Report Manager
 * المايسترو (Facade) الذي يجمع الداتا من הـ DataSet ويوجهها للمُصدر المناسب
 * ويسجل العملية في جدول الـ Reports كأرشيف للمستندات المستخرجة.
 */
class ReportManager
{
    protected DataSet $dataSet;
    protected CsvExporter $csvExporter;
    protected ExcelExporter $excelExporter;
    protected PdfExporter $pdfExporter;
    protected TenantContext $tenant;
    protected AuthManager $auth;

    public function __construct(
        DataSet $dataSet,
        CsvExporter $csvExporter,
        ExcelExporter $excelExporter,
        PdfExporter $pdfExporter,
        TenantContext $tenant,
        AuthManager $auth
    ) {
        $this->dataSet = $dataSet;
        $this->csvExporter = $csvExporter;
        $this->excelExporter = $excelExporter;
        $this->pdfExporter = $pdfExporter;
        $this->tenant = $tenant;
        $this->auth = $auth;
    }

    /**
     * تشغيل وتصدير تقرير.
     *
     * @param ReportDefinition $definition
     * @param array<ReportFilter> $filters
     * @param string $format 'csv', 'excel', 'pdf'
     * @return string مسار الملف النهائي
     * @throws BusinessException
     */
    public function generate(ReportDefinition $definition, array $filters, string $format): string
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()?->id ?? 0;

        // 1. جلب البيانات باستخدام تقنية הـ Generator (لتوفير الذاكرة)
        $dataGenerator = $this->dataSet->fetch($definition, $filters, $companyId);
        
        $columns = $definition->getAttribute('columns');
        if (!is_array($columns)) {
            throw new BusinessException("Report definition has invalid column mapping.");
        }

        $filename = "Report_" . preg_replace('/[^a-zA-Z0-9]/', '_', $definition->getAttribute('name')) . "_" . time();

        // 2. التصدير
        $path = match (strtolower($format)) {
            'csv'   => $this->csvExporter->export($dataGenerator, $columns, $filename),
            'excel' => $this->excelExporter->export($dataGenerator, $columns, $filename),
            'pdf'   => $this->pdfExporter->export($dataGenerator, $columns, $definition->getAttribute('name'), $filename),
            default => throw new BusinessException("Unsupported export format: {$format}"),
        };

        // 3. (يتم هنا عادةً استدعاء Repository لحفظ الكيان Report في الداتابيز للتوثيق)
        // $this->reportRepository->create([ ... ]);

        return $path;
    }
}