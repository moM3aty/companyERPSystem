<?php
// app/controllers/ReportController.php

class ReportController extends Controller {
    
    private Report $reportModel;

    public function __construct() {
        // حماية القسم للإدارة والمحاسبين فقط
        $this->requireAnyRole(['admin', 'manager']);
        $this->reportModel = $this->model('Report');
    }

    public function index(): void {
        $currentYear = (int)$this->getQuery('year', date('Y'));
        
        $salesByMonth = $this->reportModel->getSalesByMonth($currentYear);
        $expensesByCategory = $this->reportModel->getExpensesByCategory($currentYear);
        $topCustomers = $this->reportModel->getTopCustomers(5);
        $topProducts = $this->reportModel->getTopSellingProducts(5);
        $inventoryValuation = $this->reportModel->getInventoryValuation();

        // تحضير بيانات المخطط الدائري (Pie Chart) للمصروفات
        $expenseLabels = [];
        $expenseValues = [];
        foreach ($expensesByCategory as $exp) {
            $expenseLabels[] = $exp->category_name;
            $expenseValues[] = $exp->total_amount;
        }

        $data = [
            'title' => 'التقارير التحليلية ولوحات القيادة',
            'current_year' => $currentYear,
            'sales_data' => array_values($salesByMonth),
            'expense_labels' => json_encode($expenseLabels),
            'expense_values' => json_encode($expenseValues),
            'top_customers' => $topCustomers,
            'top_products' => $topProducts,
            'inventory_valuation' => $inventoryValuation,
            'breadcrumb' => [
                ['label' => 'التقارير والإحصائيات', 'url' => 'report/index']
            ]
        ];
        
        ob_start();
        $this->view('reports/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function sales(): void {
        $startDate = $this->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->getQuery('end_date', date('Y-m-t'));
        
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
            
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            
            $output = fopen('php://output', 'w');
            // دعم قراءة الملف باللغة العربية في Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, ['رقم الفاتورة', 'العميل', 'المبلغ الإجمالي', 'التاريخ', 'مندوب المبيعات']);
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