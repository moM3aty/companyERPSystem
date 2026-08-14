<?php
// app/controllers/ReportController.php

class ReportController extends Controller {
    
    private $reportModel;

    public function __construct() {
        $this->requireAnyRole(['admin', 'manager', 'editor', 'super_admin']);
        $this->reportModel = $this->model('Report');
    }

    public function index() {
        $data = [
            'title' => 'التقارير الذكية والمحاسبية',
            'breadcrumb' => [['label' => 'الإدارة والدعم', 'url' => '#'], ['label' => 'التقارير', 'url' => 'report/index']]
        ];
        ob_start(); $this->view('reports/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
    
    public function sales() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        
        $salesData = $this->reportModel->getSalesReport($startDate, $endDate);
        $topProducts = $this->reportModel->getTopSellingProducts($startDate, $endDate);
        
        $data = [
            'title' => 'تقرير المبيعات والضرائب',
            'sales' => $salesData, 'top_products' => $topProducts, 'start_date' => $startDate, 'end_date' => $endDate,
            'breadcrumb' => [['label' => 'التقارير', 'url' => 'report/index'], ['label' => 'مبيعات', 'url' => '#']]
        ];
        ob_start(); $this->view('reports/sales', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function hr() {
        $month = $_GET['month'] ?? date('Y-m');
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $hrData = $this->reportModel->getHrReport($startDate, $endDate);

        $data = [
            'title' => 'تقرير الموارد البشرية',
            'hr_data' => $hrData, 'selected_month' => $month,
            'breadcrumb' => [['label' => 'التقارير', 'url' => 'report/index'], ['label' => 'الموارد البشرية', 'url' => '#']]
        ];
        ob_start(); $this->view('reports/hr', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function purchases() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        $purchasesData = $this->reportModel->getPurchasesReport($startDate, $endDate);
        $supplierData = $this->reportModel->getSupplierReport($startDate, $endDate);

        $data = [
            'title' => 'تقرير المشتريات والموردين',
            'purchases' => $purchasesData, 'suppliers' => $supplierData, 'start_date' => $startDate, 'end_date' => $endDate,
            'breadcrumb' => [['label' => 'التقارير', 'url' => 'report/index'], ['label' => 'المشتريات', 'url' => '#']]
        ];
        ob_start(); $this->view('reports/purchases', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function incomeStatement() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        $incomeData = $this->reportModel->getIncomeStatement($startDate, $endDate);

        $data = [
            'title' => 'قائمة الدخل (Income Statement)',
            'income_data' => $incomeData, 'start_date' => $startDate, 'end_date' => $endDate,
            'breadcrumb' => [['label' => 'التقارير', 'url' => 'report/index'], ['label' => 'قائمة الدخل', 'url' => '#']]
        ];
        ob_start(); $this->view('reports/income_statement', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function balanceSheet() {
        $asOfDate = $_GET['as_of_date'] ?? date('Y-m-d');

        $assets = $this->reportModel->getAccountBalancesByType('Asset');
        $liabilities = $this->reportModel->getAccountBalancesByType('Liability');
        $equity = $this->reportModel->getAccountBalancesByType('Equity');

        $data = [
            'title' => 'الميزانية العمومية (Balance Sheet)',
            'assets' => $assets, 'liabilities' => $liabilities, 'equity' => $equity, 'as_of_date' => $asOfDate,
            'breadcrumb' => [['label' => 'التقارير', 'url' => 'report/index'], ['label' => 'الميزانية', 'url' => '#']]
        ];
        ob_start(); $this->view('reports/balance_sheet', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
}