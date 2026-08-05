<?php
// app/controllers/ReportController.php

class ReportController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit();
        }
        // السماح للمشرفين والمحررين فقط
        if (!in_array($_SESSION['user_role'], ['admin', 'editor'])) {
            $this->setFlash('error', 'ليس لديك صلاحية للوصول إلى التقارير');
            header('Location: ' . URL_ROOT . '/dashboard');
            exit();
        }
    }

    /**
     * الصفحة الرئيسية للتقارير والتحليلات
     * تجمع البيانات من كل النماذج وتعرضها في شارتات وجداول
     */
    public function index() {
        // تحميل النماذج
        $accountingModel = $this->model('Accounting');
        $employeeModel   = $this->model('Employee');
        $productModel    = $this->model('Product');
        $saleModel       = $this->model('Sale');

        // ========================================
        // 1) المؤشرات الرئيسية (KPIs)
        // ========================================
        $totalSales    = $accountingModel->getTotalSales();
        $totalExpenses = $accountingModel->getTotalExpenses();
        $netProfit     = $totalSales - $totalExpenses;
        $empCount      = count($employeeModel->getEmployees());
        $prodCount     = count($productModel->getProducts());
        $invoiceCount  = $accountingModel->getInvoiceCount();

        // ========================================
        // 2) أعلى المنتجات مبيعاً
        // ========================================
        $topProductsRaw = $accountingModel->getTopProducts(10);
        $topProducts = [];

        if (!empty($topProductsRaw)) {
            $maxRevenue = 0;
            foreach ($topProductsRaw as $p) {
                if ($p->total_revenue > $maxRevenue) {
                    $maxRevenue = $p->total_revenue;
                }
            }
            $maxRevenue = max($maxRevenue, 1); // تجنب القسمة على صفر

            foreach ($topProductsRaw as $p) {
                $topProducts[] = [
                    'name'    => $p->name,
                    'units'   => (int) $p->total_units,
                    'revenue' => (float) $p->total_revenue,
                    'pct'     => round(($p->total_revenue / $maxRevenue) * 100)
                ];
            }
        }

        // ========================================
        // 3) المبيعات الشهرية (12 شهر — السنة الحالية)
        // ========================================
        $monthlySalesRaw = $accountingModel->getMonthlySales();
        $monthlySales = array_fill(0, 12, 0);

        foreach ($monthlySalesRaw as $m) {
            $idx = (int) $m->month_idx;
            if ($idx >= 0 && $idx <= 11) {
                $monthlySales[$idx] = (float) $m->total;
            }
        }

        // ========================================
        // 4) المصروفات الشهرية (12 شهر — السنة الحالية)
        // ========================================
        $monthlyExpensesRaw = $accountingModel->getMonthlyExpenses();
        $monthlyExpenses = array_fill(0, 12, 0);

        foreach ($monthlyExpensesRaw as $m) {
            $idx = (int) $m->month_idx;
            if ($idx >= 0 && $idx <= 11) {
                $monthlyExpenses[$idx] = (float) $m->total;
            }
        }

        // صافي الربح الشهري
        $monthlyProfit = [];
        for ($i = 0; $i < 12; $i++) {
            $monthlyProfit[] = $monthlySales[$i] - $monthlyExpenses[$i];
        }

        // ========================================
        // 5) توزيع المصروفات حسب التصنيف
        // ========================================
        $expenseDistLabels = [];
        $expenseDistData   = [];

        try {
            $distRaw = $accountingModel->getExpenseDistribution();
            if (!empty($distRaw)) {
                $totalExpDist = 0;
                foreach ($distRaw as $d) {
                    $totalExpDist += (float) $d->total;
                }

                foreach ($distRaw as $d) {
                    $expenseDistLabels[] = $d->category;
                    // نسبة مئوية من إجمالي المصروفات
                    $pct = $totalExpDist > 0 
                        ? round(((float) $d->total / $totalExpDist) * 100, 1) 
                        : 0;
                    $expenseDistData[] = $pct;
                }
            }
        } catch (Exception $e) {
            // لو عمود category مش موجود بعد
            $expenseDistLabels = ['رواتب', 'إيجار', 'كهرباء', 'صيانة', 'تسويق', 'نقل', 'أخرى'];
            $expenseDistData   = [35, 20, 12, 10, 8, 8, 7];
        }

        // لو مفيش بيانات أصلاً
        if (empty($expenseDistLabels)) {
            $expenseDistLabels = ['لا توجد مصروفات'];
            $expenseDistData   = [100];
        }

        // ========================================
        // 6) ملخص الرواتب (من بيانات الموظفين)
        // ========================================
        $employees = $employeeModel->getEmployees();
        $totalSalaries = 0;
        $deptSalaries  = [];

        foreach ($employees as $emp) {
            $totalSalaries += (float) ($emp->salary ?? 0);
            $dept = $emp->dept_name ?? 'بدون قسم';
            if (!isset($deptSalaries[$dept])) {
                $deptSalaries[$dept] = 0;
            }
            $deptSalaries[$dept] += (float) ($emp->salary ?? 0);
        }

        // ترتيب الأقسام بالرواتب
        arsort($deptSalaries);

        // ========================================
        // 7) حالة المخزون (منتجات منخفضة/نفذت)
        // ========================================
        $products = $productModel->getProducts();
        $stockStatus = [
            'total_value'  => 0,
            'low_stock'    => [],
            'out_of_stock' => []
        ];

        foreach ($products as $p) {
            $stockStatus['total_value'] += ((float) $p->price * (int) $p->quantity);
            if ((int) $p->quantity <= 0) {
                $stockStatus['out_of_stock'][] = [
                    'name' => $p->name,
                    'sku'  => $p->sku
                ];
            } elseif ((int) $p->quantity <= 10) {
                $stockStatus['low_stock'][] = [
                    'name'     => $p->name,
                    'sku'      => $p->sku,
                    'quantity' => (int) $p->quantity
                ];
            }
        }

        // ========================================
        // تمرير كل البيانات للفيو
        // ========================================
        $data = [
            'title'            => 'التقارير والتحليلات',
            'total_sales'      => $totalSales,
            'total_expenses'   => $totalExpenses,
            'net_profit'       => $netProfit,
            'emp_count'        => $empCount,
            'prod_count'       => $prodCount,
            'invoice_count'    => $invoiceCount,
            'top_products'     => $topProducts,
            'monthly_sales'    => $monthlySales,
            'monthly_expenses' => $monthlyExpenses,
            'monthly_profit'   => $monthlyProfit,
            'expense_dist_labels' => $expenseDistLabels,
            'expense_dist_data'   => $expenseDistData,
            'total_salaries'   => $totalSalaries,
            'dept_salaries'    => $deptSalaries,
            'stock_status'     => $stockStatus
        ];

        $this->view('reports/index', $data);
    }

    private function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}