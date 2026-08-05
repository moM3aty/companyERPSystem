<?php
// المسار: app/controllers/ReportController.php

class ReportController extends Controller {
    
    /** @var Report */
    private Report $reportModel;

    public function __construct() {
        // حماية الوصول: فقط للإدارة العليا أو مديري الأقسام
        $this->requireAnyRole(['admin', 'manager']);
        $this->reportModel = $this->model('Report');
    }

    public function index(): void {
        $currentYear = (int)date('Y');
        
        // جلب البيانات للمخططات
        $salesByMonth = $this->reportModel->getSalesByMonth($currentYear);
        $expensesByCategory = $this->reportModel->getExpensesByCategory($currentYear);
        $topCustomers = $this->reportModel->getTopCustomers();
        $inventoryValuation = $this->reportModel->getInventoryValuation();

        // تجهيز التسميات والقيم لمخطط المصروفات (Doughnut Chart)
        $expenseLabels = [];
        $expenseValues = [];
        foreach ($expensesByCategory as $exp) {
            $expenseLabels[] = $exp->category_name;
            $expenseValues[] = $exp->total_amount;
        }

        $data = [
            'title' => 'التقارير التحليلية ولوحات القيادة',
            'current_year' => $currentYear,
            'sales_data' => array_values($salesByMonth), // البيانات كأرقام فقط
            'expense_labels' => json_encode($expenseLabels),
            'expense_values' => json_encode($expenseValues),
            'top_customers' => $topCustomers,
            'inventory_valuation' => $inventoryValuation,
            'breadcrumb' => [
                ['label' => 'التقارير', 'url' => 'report/index']
            ]
        ];
        
        ob_start();
        $this->view('reports/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function sales(): void {
        $startDate = $this->getQuery('start_date', date('Y-m-01')); // أول الشهر
        $endDate = $this->getQuery('end_date', date('Y-m-t')); // آخر الشهر
        
        $sales = $this->reportModel->getDetailedSalesReport($startDate, $endDate);
        
        $data = [
            'title' => 'تقرير المبيعات التفصيلي',
            'sales' => $sales,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'breadcrumb' => [
                ['label' => 'التقارير', 'url' => 'report/index'],
                ['label' => 'المبيعات', 'url' => '#']
            ]
        ];

        ob_start();
        $this->view('reports/sales', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function exportCsv(string $type = 'sales'): void {
        if ($type === 'sales') {
            $startDate = $this->getQuery('start_date', date('Y-m-01'));
            $endDate = $this->getQuery('end_date', date('Y-m-t'));
            
            $sales = $this->reportModel->getDetailedSalesReport($startDate, $endDate);
            $filename = "Sales_Report_{$startDate}_to_{$endDate}.csv";
            
            // إعداد الترويسات لتحميل الملف
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            
            // فتح مجرى الإخراج
            $output = fopen('php://output', 'w');
            
            // دعم اللغة العربية في الإكسيل (BOM)
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // كتابة العناوين
            fputcsv($output, ['رقم الفاتورة', 'العميل', 'المبلغ الإجمالي', 'التاريخ', 'مندوب المبيعات']);
            
            // كتابة البيانات
            foreach ($sales as $row) {
                fputcsv($output, [
                    $row->invoice_number,
                    $row->customer_name,
                    $row->total_amount,
                    $row->created_at,
                    $row->sales_rep ?? 'غير محدد'
                ]);
            }
            fclose($output);
            exit;
        }
        
        $this->redirect('report/index');
    }
}