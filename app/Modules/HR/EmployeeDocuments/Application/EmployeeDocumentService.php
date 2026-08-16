<?php
// Path: app/Modules/HR/EmployeeDocuments/Application/EmployeeDocumentService.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeDocuments\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Files\FileManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Employee Documents
 * يدير رفع وحفظ مستندات الموظف (الإقامة، الجواز) بشكل آمن ومحمي.
 */
class EmployeeDocumentService
{
    protected DatabaseManager $db;
    protected FileManager $fileManager;

    public function __construct(DatabaseManager $db, FileManager $fileManager)
    {
        $this->db = $db;
        $this->fileManager = $fileManager;
    }

    public function uploadDocument(array $data, array $fileInfo, int $companyId): int
    {
        $employeeId = (int) $data['employee_id'];
        
        $employee = $this->db->connection()->selectOne("SELECT id FROM hr_employees WHERE id = ? AND company_id = ?", [$employeeId, $companyId]);
        if (!$employee) {
            throw new BusinessException("Employee not found.");
        }

        // استخدام الـ FileManager لحفظ الملف بأمان في مجلد الموظفين المخصص
        $path = $this->fileManager->store($fileInfo, "hr_documents/{$companyId}/{$employeeId}");

        $this->db->connection()->insert(
            "INSERT INTO hr_employee_documents 
            (company_id, employee_id, document_type, document_number, issue_date, expiry_date, file_reference, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $companyId,
                $employeeId,
                $data['document_type'],
                $data['document_number'] ?? null,
                $data['issue_date'] ?? null,
                $data['expiry_date'] ?? null,
                $path,
                date('Y-m-d H:i:s')
            ]
        );

        return (int) $this->db->connection()->getPdo()->lastInsertId();
    }
}