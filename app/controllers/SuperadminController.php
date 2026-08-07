<?php
// app/controllers/SuperadminController.php

class SuperadminController extends Controller {
    
    private SaasReport $saasModel;

    public function __construct() {
        // حماية مشددة: لا يدخل هنا إلا الـ Super Admin (المالك)
        if (!Session::isLoggedIn() || Session::getUserRole() !== 'super_admin') {
            Session::setFlash('error', 'غير مصرح لك بالدخول إلى لوحة التحكم المتقدمة للنظام.');
            $this->redirect('dashboard/index');
        }
        $this->saasModel = $this->model('SaasReport');
    }

    public function dashboard(): void {
        $metrics = $this->saasModel->getMetrics();
        $historicalData = $this->saasModel->getHistoricalMRR(6); // بيانات آخر 6 أشهر
        $packageDist = $this->saasModel->getPackageDistribution();
        $recentCompanies = $this->saasModel->getRecentCompanies(5);

        // تجهيز بيانات الرسوم البيانية لتمريرها كـ JSON للـ JavaScript
        $chartData = [
            'labels' => json_encode($historicalData['labels']),
            'mrr'    => json_encode($historicalData['mrr']),
            'tenants'=> json_encode($historicalData['companies'])
        ];

        $packageLabels = [];
        $packageCounts = [];
        foreach ($packageDist as $pkg) {
            $packageLabels[] = $pkg->name;
            $packageCounts[] = $pkg->companies_count;
        }

        $data = [
            'title' => 'لوحة المالك (SaaS Intelligence)',
            'metrics' => $metrics,
            'chartData' => $chartData,
            'packageDist' => $packageDist,
            'pkgLabels' => json_encode($packageLabels),
            'pkgCounts' => json_encode($packageCounts),
            'recentCompanies' => $recentCompanies,
            'breadcrumb' => [
                ['label' => 'إدارة النظام', 'url' => '#'],
                ['label' => 'لوحة المالك (SaaS)', 'url' => 'superadmin/dashboard']
            ]
        ];

        ob_start();
        $this->view('superadmin/dashboard', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
}