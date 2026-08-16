<?php
// Path: routes/web.php

declare(strict_types=1);

use App\Core\Routing\Router;

/**
 * Web Route Registrar
 *
 * RouteServiceProvider passes the Router instance explicitly.
 */
return static function (Router $router): void {

    /**
     * Render a view from resources/views.
     */
    $view = static function (string $viewPath): void {
        $file = BASE_PATH . '/resources/views/' . ltrim($viewPath, '/');

        if (!is_file($file)) {
            throw new \RuntimeException("View file not found: {$file}");
        }

        require $file;
    };

    /**
     * Create an application-relative redirect Response.
     */
    $redirect = static function (
        string $path,
        int $status = 302
    ): \App\Core\Http\Response {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseUri = dirname($scriptName);

        if (
            $baseUri === '.'
            || $baseUri === '\\'
            || $baseUri === '/'
        ) {
            $baseUri = '';
        }

        $location = rtrim($baseUri, '/') . '/' . ltrim($path, '/');

        return new \App\Core\Http\Response(
            '',
            $status,
            [
                'Location' => $location,
            ]
        );
    };

// 1. المسار الرئيسي وحصل تحويل آلي متوافق مع المجلدات الفرعية إلى الداشبورد
$router->get('/', static function () use ($redirect): \App\Core\Http\Response {
    return $redirect('/dashboard');
});

$router->get('/dashboard', static function () use ($view): void {
    $view('dashboard/index.php');
});

// 2. مسارات المصادقة والملف الشخصي
$router->get('/login', static function () use ($view): void {
    $view('auth/login.php');
});

$router->get('/forgot-password', static function () use ($view): void {
    $view('auth/forgot-password.php');
});

$router->get('/profile', static function () use ($view): void {
    $view('profile/index.php');
});

$router->get('/profile/edit', static function () use ($view): void {
    $view('profile/edit.php');
});

$router->get('/profile/security', static function () use ($view): void {
    $view('profile/security.php');
});

$router->get('/profile/notifications', static function () use ($view): void {
    $view('profile/notifications.php');
});

$router->get('/profile/activity', static function () use ($view): void {
    $view('profile/activity.php');
});

$router->get('/profile/sessions', static function () use ($view): void {
    $view('profile/sessions.php');
});

// 3. مسارات المبيعات وعلاقات العملاء (CRM & Sales)
$router->get('/sales', static function () use ($view): void {
    $view('sales/index.php');
});

$router->get('/sales/create', static function () use ($view): void {
    $view('sales/create.php');
});

$router->get('/sales/show', static function () use ($view): void {
    $view('sales/show.php');
});

$router->get('/crm/customers', static function () use ($view): void {
    $view('crm/customers.php');
});

$router->get('/crm/customers/create', static function () use ($view): void {
    $view('crm/create.php');
});

$router->get('/crm/customers/show', static function () use ($view): void {
    $view('crm/show.php');
});

$router->get('/crm/customers/edit', static function () use ($view): void {
    $view('crm/edit.php');
});

$router->get('/crm/leads', static function () use ($view): void {
    $view('crm/leads/index.php');
});

$router->get('/crm/opportunities/kanban', static function () use ($view): void {
    $view('crm/opportunities/kanban.php');
});

// 4. مسارات المشتريات والتعاقدات (Purchasing)
$router->get('/purchasing', static function () use ($view): void {
    $view('purchasing/index.php');
});

$router->get('/purchasing/orders', static function () use ($view): void {
    $view('purchasing/orders.php');
});

$router->get('/purchasing/orders/show', static function () use ($view): void {
    $view('purchasing/orders/show.php');
});

$router->get('/purchasing/suppliers', static function () use ($view): void {
    $view('purchasing/suppliers/index.php');
});

$router->get('/purchasing/requisitions', static function () use ($view): void {
    $view('purchasing/requisitions/index.php');
});

$router->get('/purchasing/rfqs', static function () use ($view): void {
    $view('purchasing/rfqs/index.php');
});

$router->get('/purchasing/invoices', static function () use ($view): void {
    $view('purchasing/invoices/index.php');
});

// 5. مسارات المخازن والمنتجات (Inventory)
$router->get('/inventory', static function () use ($view): void {
    $view('inventory/index.php');
});

$router->get('/inventory/products', static function () use ($view): void {
    $view('inventory/products.php');
});

$router->get('/inventory/products/create', static function () use ($view): void {
    $view('inventory/products/create.php');
});

$router->get('/inventory/products/show', static function () use ($view): void {
    $view('inventory/products/show.php');
});

$router->get('/inventory/warehouses', static function () use ($view): void {
    $view('inventory/warehouses/index.php');
});

$router->get('/inventory/stock-transfers', static function () use ($view): void {
    $view('inventory/stock-transfers/index.php');
});

$router->get('/inventory/adjustments', static function () use ($view): void {
    $view('inventory/adjustments/index.php');
});

$router->get('/inventory/adjustments/create', static function () use ($view): void {
    $view('inventory/adjustments/create.php');
});

// 6. مسارات المحاسبة والمالية (Accounting)
$router->get('/accounting', static function () use ($view): void {
    $view('accounting/index.php');
});

$router->get('/accounting/invoices', static function () use ($view): void {
    $view('accounting/invoices.php');
});

$router->get('/accounting/chart-of-accounts', static function () use ($view): void {
    $view('accounting/chart-of-accounts.php');
});

$router->get('/accounting/journal-entries', static function () use ($view): void {
    $view('accounting/journal-entries/index.php');
});

$router->get('/accounting/journal-entries/create', static function () use ($view): void {
    $view('accounting/journal-entries/create.php');
});

$router->get('/accounting/reconciliation', static function () use ($view): void {
    $view('accounting/reconciliation.php');
});

$router->get('/accounting/cost-centers', static function () use ($view): void {
    $view('accounting/cost-centers/index.php');
});

$router->get('/accounting/taxes', static function () use ($view): void {
    $view('accounting/taxes/index.php');
});

// 7. مسارات الخزانة والبنوك (Treasury)
$router->get('/treasury', static function () use ($view): void {
    $view('treasury/dashboard.php');
});

$router->get('/treasury/dashboard', static function () use ($view): void {
    $view('treasury/dashboard.php');
});

$router->get('/treasury/receipts', static function () use ($view): void {
    $view('treasury/receipts/index.php');
});

$router->get('/treasury/payments', static function () use ($view): void {
    $view('treasury/payments/index.php');
});

$router->get('/treasury/transfers', static function () use ($view): void {
    $view('treasury/transfers.php');
});

// 8. مسارات الموارد البشرية والرواتب (HR & Payroll)
$router->get('/hr', static function () use ($view): void {
    $view('hr/index.php');
});

$router->get('/hr/employees', static function () use ($view): void {
    $view('hr/employees.php');
});

$router->get('/hr/employees/create', static function () use ($view): void {
    $view('hr/employees/create.php');
});

$router->get('/hr/attendance', static function () use ($view): void {
    $view('hr/attendance/index.php');
});

$router->get('/hr/leaves', static function () use ($view): void {
    $view('hr/leaves/index.php');
});

$router->get('/hr/payroll/dashboard', static function () use ($view): void {
    $view('hr/payroll/dashboard.php');
});

$router->get('/hr/payroll/settings', static function () use ($view): void {
    $view('hr/payroll/settings.php');
});

$router->get('/payroll/runs', static function () use ($view): void {
    $view('payroll/runs/index.php');
});

// 9. مسارات المشاريع وإدارة المهام (Projects)
$router->get('/projects', static function () use ($view): void {
    $view('projects/index.php');
});

$router->get('/projects/create', static function () use ($view): void {
    $view('projects/create.php');
});

$router->get('/projects/kanban', static function () use ($view): void {
    $view('projects/kanban.php');
});

$router->get('/projects/timesheets', static function () use ($view): void {
    $view('projects/timesheets/index.php');
});

// 10. مسارات التصنيع والإنتاج (Manufacturing)
$router->get('/manufacturing', static function () use ($view): void {
    $view('manufacturing/dashboard.php');
});

$router->get('/manufacturing/bom', static function () use ($view): void {
    $view('manufacturing/bom/index.php');
});

$router->get('/manufacturing/bom/create', static function () use ($view): void {
    $view('manufacturing/bom/create.php');
});

$router->get('/manufacturing/work-orders', static function () use ($view): void {
    $view('manufacturing/work-orders/index.php');
});

$router->get('/manufacturing/work-centers', static function () use ($view): void {
    $view('manufacturing/work-centers/index.php');
});

// 11. مسارات الصيانة والأسطول والأصول (Maintenance, Fleet, Assets)
$router->get('/maintenance', static function () use ($view): void {
    $view('maintenance/dashboard.php');
});

$router->get('/maintenance/work-orders', static function () use ($view): void {
    $view('maintenance/work-orders/index.php');
});

$router->get('/maintenance/work-orders/create', static function () use ($view): void {
    $view('maintenance/work-orders/create.php');
});

$router->get('/fleet', static function () use ($view): void {
    $view('fleet/dashboard.php');
});

$router->get('/fleet/vehicles', static function () use ($view): void {
    $view('fleet/vehicles/index.php');
});

$router->get('/fleet/trips', static function () use ($view): void {
    $view('fleet/trips/index.php');
});

$router->get('/assets', static function () use ($view): void {
    $view('assets/index.php');
});

$router->get('/assets/create', static function () use ($view): void {
    $view('assets/create.php');
});

$router->get('/assets/show', static function () use ($view): void {
    $view('assets/show.php');
});

// 12. نقاط البيع والمردودات (POS & Returns)
$router->get('/pos/terminals', static function () use ($view): void {
    $view('pos/terminals/index.php');
});

$router->get('/shifts', static function () use ($view): void {
    $view('shifts/index.php');
});

$router->get('/shifts/open', static function () use ($view): void {
    $view('shifts/open.php');
});

$router->get('/shifts/close', static function () use ($view): void {
    $view('shifts/close.php');
});

$router->get('/shifts/show', static function () use ($view): void {
    $view('shifts/show.php');
});

$router->get('/returns', static function () use ($view): void {
    $view('returns/index.php');
});

$router->get('/returns/create', static function () use ($view): void {
    $view('returns/create.php');
});

$router->get('/returns/edit', static function () use ($view): void {
    $view('returns/edit.php');
});

// 13. التقارير وإحصائيات الأعمال (Reports & Analytics)
$router->get('/reports', static function () use ($view): void {
    $view('reports/dashboard.php');
});

$router->get('/reports/financial', static function () use ($view): void {
    $view('reports/financial/index.php');
});

$router->get('/reports/sales', static function () use ($view): void {
    $view('reports/sales/index.php');
});

$router->get('/reports/inventory', static function () use ($view): void {
    $view('reports/inventory/index.php');
});

$router->get('/reports/purchasing', static function () use ($view): void {
    $view('reports/purchasing/index.php');
});

$router->get('/reports/hr', static function () use ($view): void {
    $view('reports/hr/index.php');
});

$router->get('/reports/payroll', static function () use ($view): void {
    $view('reports/payroll/index.php');
});

$router->get('/reports/projects', static function () use ($view): void {
    $view('reports/projects/index.php');
});

$router->get('/reports/custom', static function () use ($view): void {
    $view('reports/custom/index.php');
});

$router->get('/reports/favorites', static function () use ($view): void {
    $view('reports/favorites.php');
});

$router->get('/reports/scheduled', static function () use ($view): void {
    $view('reports/scheduled.php');
});

$router->get('/reports/exports', static function () use ($view): void {
    $view('reports/exports.php');
});

// 14. لوحة الإدارة والبحث (Administration & Search)
$router->get('/admin', static function () use ($view): void {
    $view('admin/settings/general.php');
});

$router->get('/admin/branches', static function () use ($view): void {
    $view('admin/branches/index.php');
});

$router->get('/admin/users', static function () use ($view): void {
    $view('admin/users/index.php');
});

$router->get('/admin/roles', static function () use ($view): void {
    $view('admin/roles.php');
});

$router->get('/search', static function () use ($view): void {
    $view('search/index.php');
});

$router->get('/search/results', static function () use ($view): void {
    $view('search/results.php');
});

$router->get('/notifications', static function () use ($view): void {
    $view('notifications/index.php');
});

$router->get('/notifications/templates', static function () use ($view): void {
    $view('notifications/templates.php');
});

$router->get('/closing', static function () use ($view): void {
    $view('closing/index.php');
});

$router->get('/closing/show', static function () use ($view): void {
    $view('closing/show.php');
});

// 15. إدارة الشركات والمجموعة (Enterprise Feature Stack)
$router->get('/enterprise/consolidation/dashboard', static function () use ($view): void {
    $view('enterprise/consolidation/dashboard.php');
});

$router->get('/enterprise/consolidation/groups', static function () use ($view): void {
    $view('enterprise/consolidation/groups.php');
});

$router->get('/enterprise/consolidation/eliminations', static function () use ($view): void {
    $view('enterprise/consolidation/eliminations.php');
});

$router->get('/enterprise/consolidation/periods', static function () use ($view): void {
    $view('enterprise/consolidation/periods.php');
});

$router->get('/enterprise/consolidation/reports', static function () use ($view): void {
    $view('enterprise/consolidation/reports.php');
});

$router->get('/enterprise/intercompany/dashboard', static function () use ($view): void {
    $view('enterprise/intercompany/dashboard.php');
});

$router->get('/enterprise/intercompany/agreements', static function () use ($view): void {
    $view('enterprise/intercompany/agreements.php');
});

$router->get('/enterprise/intercompany/reconciliation', static function () use ($view): void {
    $view('enterprise/intercompany/reconciliation.php');
});

$router->get('/enterprise/intercompany/matching', static function () use ($view): void {
    $view('enterprise/intercompany/matching.php');
});

$router->get('/enterprise/intercompany/transactions', static function () use ($view): void {
    $view('enterprise/intercompany/transactions.php');
});

$router->get('/supply-chain', static function () use ($view): void {
    $view('enterprise/supply-chain/dashboard.php');
});

$router->get('/supply-chain/forecasting', static function () use ($view): void {
    $view('enterprise/supply-chain/forecasting.php');
});

$router->get('/supply-chain/safety-stock', static function () use ($view): void {
    $view('enterprise/supply-chain/safety-stock.php');
});

$router->get('/supply-chain/reorder-rules', static function () use ($view): void {
    $view('enterprise/supply-chain/reorder-rules.php');
});

$router->get('/supply-chain/landed-cost', static function () use ($view): void {
    $view('enterprise/supply-chain/landed-cost.php');
});

$router->get('/supply-chain/planning', static function () use ($view): void {
    $view('enterprise/supply-chain/planning.php');
});

$router->get('/supply-chain/demand', static function () use ($view): void {
    $view('enterprise/supply-chain/demand.php');
});

$router->get('/enterprise/advanced-pricing/dashboard', static function () use ($view): void {
    $view('enterprise/advanced-pricing/dashboard.php');
});

$router->get('/enterprise/advanced-pricing/price-lists', static function () use ($view): void {
    $view('enterprise/advanced-pricing/price-lists.php');
});

$router->get('/enterprise/advanced-pricing/promotions', static function () use ($view): void {
    $view('enterprise/advanced-pricing/promotions.php');
});

$router->get('/enterprise/advanced-pricing/discounts', static function () use ($view): void {
    $view('enterprise/advanced-pricing/discounts.php');
});

$router->get('/enterprise/advanced-pricing/price-rules', static function () use ($view): void {
    $view('enterprise/advanced-pricing/price-rules.php');
});

$router->get('/enterprise/advanced-pricing/contracts', static function () use ($view): void {
    $view('enterprise/advanced-pricing/contracts.php');
});

$router->get('/enterprise/advanced-hr/dashboard', static function () use ($view): void {
    $view('enterprise/advanced-hr/dashboard.php');
});

$router->get('/enterprise/advanced-hr/competencies', static function () use ($view): void {
    $view('enterprise/advanced-hr/competencies.php');
});

$router->get('/enterprise/advanced-hr/training', static function () use ($view): void {
    $view('enterprise/advanced-hr/training.php');
});

$router->get('/enterprise/advanced-hr/succession', static function () use ($view): void {
    $view('enterprise/advanced-hr/succession.php');
});

$router->get('/enterprise/advanced-hr/career', static function () use ($view): void {
    $view('enterprise/advanced-hr/career.php');
});

$router->get('/enterprise/advanced-hr/performance', static function () use ($view): void {
    $view('enterprise/advanced-hr/performance.php');
});

// 16. مستندات الطباعة (Print Documents)
$router->get('/documents/invoice/sales', static function () use ($view): void {
    $view('documents/invoice/sales.php');
});

$router->get('/documents/invoice/purchase', static function () use ($view): void {
    $view('documents/invoice/purchase.php');
});

$router->get('/documents/quotation/sales', static function () use ($view): void {
    $view('documents/quotation/sales.php');
});

$router->get('/documents/quotation/purchase', static function () use ($view): void {
    $view('documents/quotation/purchase.php');
});

$router->get('/documents/purchase-order/default', static function () use ($view): void {
    $view('documents/purchase-order/default.php');
});

$router->get('/documents/sales-order/default', static function () use ($view): void {
    $view('documents/sales-order/default.php');
});

$router->get('/documents/goods-receipt/default', static function () use ($view): void {
    $view('documents/goods-receipt/default.php');
});

$router->get('/documents/delivery/default', static function () use ($view): void {
    $view('documents/delivery/default.php');
});

$router->get('/documents/receipt/default', static function () use ($view): void {
    $view('documents/receipt/default.php');
});

$router->get('/documents/payment/default', static function () use ($view): void {
    $view('documents/payment/default.php');
});

$router->get('/documents/credit-note/default', static function () use ($view): void {
    $view('documents/credit-note/default.php');
});

$router->get('/documents/payslip/default', static function () use ($view): void {
    $view('documents/payslip/default.php');
});

$router->get('/sales/receipt', static function () use ($view): void {
    $view('sales/receipt.php');
});

};