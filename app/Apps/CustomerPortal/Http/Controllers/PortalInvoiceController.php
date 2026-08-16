<?php
// Path: app/Apps/CustomerPortal/Http/Controllers/PortalInvoiceController.php

declare(strict_types=1);

namespace App\Apps\CustomerPortal\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Tenant\TenantContext;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Portal Controller: Customer Portal Invoices
 * واجهة منفصلة مخصصة للعملاء (B2B/B2C) تتيح للعميل الدخول للنظام ورؤية فواتيره فقط.
 * هذا الكنترولر لا يستخدم الـ Gate الداخلي للـ ERP، بل يطبق صلاحيات خاصة بالعميل.
 */
class PortalInvoiceController extends Controller
{
    protected DatabaseManager $db;
    protected TenantContext $tenant;

    public function __construct(DatabaseManager $db, TenantContext $tenant)
    {
        $this->db = $db;
        $this->tenant = $tenant;
        
        // Middleware مخصص لبوابة العملاء (يتحقق من Customer JWT بدلاً من User Session)
        $this->middleware(['api', 'portal_auth', 'tenant']);
    }

    /**
     * جلب الفواتير الخاصة بالعميل المسجل دخول حالياً فقط.
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        
        // افتراض أن portal_auth قام بحقن الـ customer_id في الـ Request
        $customerId = (int) $request->server('HTTP_X_CUSTOMER_ID'); 

        if (!$customerId) {
            return \App\Core\Api\ApiError::unauthorized('Unauthorized access.');
        }

        $sql = "SELECT id, invoice_no, invoice_date, due_date, grand_total, paid_amount, status 
                FROM sales_invoices 
                WHERE company_id = ? AND customer_id = ? AND status IN ('posted', 'paid')
                ORDER BY invoice_date DESC";

        $invoices = $this->db->connection()->select($sql, [$companyId, $customerId]);

        return ApiResponse::success($invoices, 'Your invoices retrieved successfully.');
    }
}