<?php
// Path: app/Modules/HR/EmployeeSelfService/Http/Controllers/EssPayslipController.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Exceptions\AuthorizationException;
use App\Core\Auth\AuthManager;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: Employee Self Service (ESS) - Payslips
 * يتيح للموظف استعراض وتحميل قسائم الراتب الخاصة به فقط بأمان تام.
 */
class EssPayslipController extends Controller
{
    protected DatabaseManager $db;
    protected AuthManager $auth;

    public function __construct(DatabaseManager $db, AuthManager $auth)
    {
        $this->db = $db;
        $this->auth = $auth;
        
        // لا نحتاج للـ Gate المعقدة هنا، هذا المسار متاح لأي موظف مسجل دخول
        $this->middleware(['api', 'auth']); 
    }

    /**
     * استعراض قسائم الراتب للموظف الحالي.
     */
    public function getMyPayslips(Request $request): JsonResponse
    {
        $user = $this->auth->user();

        if (!$user || !$user->employeeId) {
            throw new AuthorizationException("Your user account is not linked to an employee profile.");
        }

        // جلب قسائم الراتب من المسيرات المعتمدة والمرحلة فقط (status = posted)
        $sql = "SELECT p.id, r.run_period, p.basic_salary, p.allowances, p.deductions, p.net_salary, p.details 
                FROM payroll_payslips p
                JOIN payroll_runs r ON p.payroll_run_id = r.id
                WHERE p.employee_id = ? AND r.status = 'posted'
                ORDER BY r.run_period DESC";

        $payslips = $this->db->connection()->select($sql, [$user->employeeId]);

        // تنسيق الـ JSON المخزن في الـ details
        $formattedPayslips = array_map(function($payslip) {
            $payslip['details'] = json_decode($payslip['details'], true);
            return $payslip;
        }, $payslips);

        return ApiResponse::success($formattedPayslips, 'Your payslips retrieved successfully.');
    }
}