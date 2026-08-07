<?php
// app/models/SaasReport.php

class SaasReport extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function getMetrics(): array {
        // 1. حساب الإيرادات الشهرية المتكررة (MRR) للشركات النشطة (باستثناء الشركة الافتراضية id=1)
        $this->db->query("SELECT COALESCE(SUM(p.price_monthly), 0) as mrr 
                          FROM companies c 
                          LEFT JOIN saas_packages p ON c.package_id = p.id 
                          WHERE c.status = 'active' AND c.id != 1");
        $mrr = (float)($this->db->single()->mrr ?? 0);
        
        // 2. حساب الإيرادات السنوية المتوقعة (ARR)
        $arr = $mrr * 12;

        // 3. عدد الشركات النشطة
        $this->db->query("SELECT COUNT(id) as total FROM companies WHERE status = 'active' AND id != 1");
        $activeTenants = (int)($this->db->single()->total ?? 0);

        // 4. عدد الشركات الموقوفة (Churn / Suspended)
        $this->db->query("SELECT COUNT(id) as total FROM companies WHERE status = 'suspended' AND id != 1");
        $suspendedTenants = (int)($this->db->single()->total ?? 0);

        // 5. حساب نسبة النمو في MRR (الشهر الحالي مقارنة بالشهر السابق)
        $currentMonthStart = date('Y-m-01');
        
        $this->db->query("SELECT COALESCE(SUM(p.price_monthly), 0) as mrr 
                          FROM companies c 
                          LEFT JOIN saas_packages p ON c.package_id = p.id 
                          WHERE c.status = 'active' AND c.id != 1 AND c.created_at < :curr_month_start");
        $this->db->bind(':curr_month_start', $currentMonthStart);
        $lastMonthMrr = (float)($this->db->single()->mrr ?? 0);

        $growthRate = 0;
        if ($lastMonthMrr > 0) {
            $growthRate = (($mrr - $lastMonthMrr) / $lastMonthMrr) * 100;
        } elseif ($mrr > 0) {
            $growthRate = 100; // نمو بنسبة 100% إذا لم تكن هناك إيرادات في الشهر الماضي
        }

        return [
            'mrr' => $mrr,
            'arr' => $arr,
            'active_tenants' => $activeTenants,
            'suspended_tenants' => $suspendedTenants,
            'mrr_growth' => round($growthRate, 1)
        ];
    }

    public function getHistoricalMRR(int $months = 6): array {
        $data = ['labels' => [], 'mrr' => [], 'companies' => []];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $endOfMonth = date('Y-m-t 23:59:59', strtotime("-$i months"));
            $monthLabel = date('M Y', strtotime("-$i months"));
            
            // حساب الـ MRR التراكمي حتى نهاية ذلك الشهر
            $this->db->query("SELECT COALESCE(SUM(p.price_monthly), 0) as total_mrr, COUNT(c.id) as total_companies 
                              FROM companies c 
                              LEFT JOIN saas_packages p ON c.package_id = p.id 
                              WHERE c.status = 'active' AND c.id != 1 AND c.created_at <= :end_date");
            $this->db->bind(':end_date', $endOfMonth);
            $result = $this->db->single();
            
            $data['labels'][] = $monthLabel;
            $data['mrr'][] = (float)($result->total_mrr ?? 0);
            $data['companies'][] = (int)($result->total_companies ?? 0);
        }
        return $data;
    }

    public function getPackageDistribution(): array {
        $this->db->query("SELECT p.name, p.price_monthly, COUNT(c.id) as companies_count 
                          FROM saas_packages p 
                          LEFT JOIN companies c ON c.package_id = p.id AND c.status = 'active' AND c.id != 1
                          GROUP BY p.id, p.name, p.price_monthly
                          ORDER BY p.price_monthly ASC");
        return $this->db->resultSet();
    }

    public function getRecentCompanies(int $limit = 5): array {
        $sql = "SELECT c.*, p.name as package_name, p.price_monthly 
                FROM companies c 
                LEFT JOIN saas_packages p ON c.package_id = p.id 
                WHERE c.id != 1 
                ORDER BY c.created_at DESC 
                LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}