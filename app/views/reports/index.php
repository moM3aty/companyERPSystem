<?php
// app/views/reports/index.php
 $title = $data['title'] ?? 'التقارير والتحليلات';
 $totalSales = $data['total_sales'] ?? 0;
 $totalExpenses = $data['total_expenses'] ?? 0;
 $netProfit = $data['net_profit'] ?? 0;
 $empCount = $data['emp_count'] ?? 0;
 $prodCount = $data['prod_count'] ?? 0;
 $invoiceCount = $data['invoice_count'] ?? 0;
 $topProducts = $data['top_products'] ?? [];
 $monthlySales = $data['monthly_sales'] ?? array_fill(0, 12, 0);
 $monthlyExpenses = $data['monthly_expenses'] ?? array_fill(0, 12, 0);
 $monthlyProfit = $data['monthly_profit'] ?? array_fill(0, 12, 0);
 $expDistLabels = $data['expense_dist_labels'] ?? ['لا توجد بيانات'];
 $expDistData = $data['expense_dist_data'] ?? [100];
 $deptSalaries = $data['dept_salaries'] ?? [];
 $stockStatus = $data['stock_status'] ?? ['total_value' => 0, 'low_stock' => [], 'out_of_stock' => []];
 $profitMargin = $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 1) : 0;

// ألوان الشارتات
 $chartColors = ['#8b5cf6','#06b6d4','#f59e0b','#ef4444','#22c55e','#14b8a6','#ec4899','#f97316','#6366f1','#84cc16'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary: #14b8a6; --primary-dark: #0d9488; --primary-light: #ccfbf1;
            --accent: #f59e0b; --accent-light: #fef3c7;
            --success: #22c55e; --success-light: #dcfce7;
            --danger: #ef4444; --danger-light: #fee2e2;
            --info: #06b6d4; --info-light: #cffafe;
            --purple: #8b5cf6; --purple-light: #ede9fe;
            --sidebar-w: 272px; --topbar-h: 68px;
            --page-bg: #f1f5f9; --card-bg: #ffffff;
            --text-dark: #0f172a; --text-body: #475569; --text-muted: #94a3b8;
            --border: #e2e8f0; --radius: 14px; --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06); --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; }

        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%); z-index: 100; display: flex; flex-direction: column; transition: transform 0.3s ease; border-left: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { padding: 24px 24px 20px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar-brand .s-logo { width: 42px; height: 42px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; flex-shrink: 0; }
        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: var(--radius-sm); color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s ease; position: relative; }
        .nav-link i { width: 20px; text-align: center; font-size: 15px; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }
        .nav-link.active { background: rgba(20,184,166,0.1); color: var(--primary); font-weight: 600; }
        .nav-link.active::before { content: ''; position: absolute; right: -12px; top: 50%; transform: translateY(-50%); width: 3px; height: 24px; background: var(--primary); border-radius: 0 4px 4px 0; }
        .sidebar-user { padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; gap: 12px; }
        .sidebar-user .su-avatar { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto; }
        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239,68,68,0.1); }

        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; }
        .topbar { height: var(--topbar-h); background: var(--card-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .page-title { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary); }
        .mobile-menu-btn { display: none; }
        .topbar-btn { width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 15px; }
        .topbar-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }
        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        /* KPI */
        .kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 28px; }
        .kpi-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); padding: 20px; transition: all 0.3s; animation: fadeUp 0.5s ease both; }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .kpi-card:nth-child(1) { animation-delay: 0s; }
        .kpi-card:nth-child(2) { animation-delay: 0.06s; }
        .kpi-card:nth-child(3) { animation-delay: 0.12s; }
        .kpi-card:nth-child(4) { animation-delay: 0.18s; }
        .kpi-card:nth-child(5) { animation-delay: 0.24s; }
        .kpi-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 17px; margin-bottom: 14px; }
        .kpi-card:nth-child(1) .kpi-icon { background: var(--success-light); color: var(--success); }
        .kpi-card:nth-child(2) .kpi-icon { background: var(--danger-light); color: var(--danger); }
        .kpi-card:nth-child(3) .kpi-icon { background: var(--primary-light); color: var(--primary-dark); }
        .kpi-card:nth-child(4) .kpi-icon { background: var(--info-light); color: var(--info); }
        .kpi-card:nth-child(5) .kpi-icon { background: var(--purple-light); color: var(--purple); }
        .kpi-label { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .kpi-value { font-size: 24px; font-weight: 800; color: var(--text-dark); font-variant-numeric: tabular-nums; }
        .kpi-sub { font-size: 11px; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
        .kpi-sub.up { color: var(--success); }
        .kpi-sub.down { color: var(--danger); }

        /* الشارتات */
        .charts-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 28px; }
        .card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.3s both; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-header h3 { font-size: 15px; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }
        .chart-wrap { position: relative; height: 300px; }

        /* شبكة ثنائية */
        .dual-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }

        /* جدول */
        .table-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.4s both; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 14px 20px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; background: #f8fafc; border-bottom: 1.5px solid var(--border); text-align: right; white-space: nowrap; }
        tbody tr { transition: background 0.15s; border-bottom: 1px solid var(--border); }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(20,184,166,0.02); }
        tbody td { padding: 14px 20px; font-size: 13.5px; }
        .rank-badge { width: 28px; height: 28px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff; }
        .rank-1 { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
        .rank-3 { background: linear-gradient(135deg, #cd7c2f, #a16207); }
        .rank-other { background: #e2e8f0; color: var(--text-muted); }
        .prod-revenue { font-weight: 700; color: var(--success); font-variant-numeric: tabular-nums; direction: ltr; }
        .pct-bar-wrap { display: flex; align-items: center; gap: 10px; min-width: 140px; }
        .pct-bar-bg { flex: 1; height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden; }
        .pct-bar-fill { height: 100%; background: var(--primary); border-radius: 3px; transition: width 0.8s ease; }
        .pct-val { font-size: 12px; font-weight: 700; color: var(--text-dark); min-width: 36px; text-align: left; }

        /* تنبيهات المخزون */
        .stock-alerts { display: flex; flex-direction: column; gap: 10px; }
        .stock-alert {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px; border-radius: var(--radius-sm);
            border: 1px solid var(--border); background: var(--card-bg);
            transition: all 0.2s;
        }
        .stock-alert:hover { box-shadow: var(--shadow-sm); }
        .stock-alert.sa-out { border-right: 3px solid var(--danger); }
        .stock-alert.sa-low { border-right: 3px solid var(--accent); }
        .sa-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .sa-out .sa-icon { background: var(--danger-light); color: var(--danger); }
        .sa-low .sa-icon { background: var(--accent-light); color: var(--accent); }
        .sa-name { font-weight: 600; color: var(--text-dark); font-size: 13px; }
        .sa-detail { font-size: 11px; color: var(--text-muted); }
        .sa-tag { margin-right: auto; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 6px; white-space: nowrap; }
        .sa-out .sa-tag { background: var(--danger-light); color: #dc2626; }
        .sa-low .sa-tag { background: var(--accent-light); color: #b45309; }
        .stock-empty { text-align: center; padding: 32px; color: var(--text-muted); font-size: 13px; }
        .stock-empty i { font-size: 32px; display: block; margin-bottom: 10px; color: var(--border); }

        /* رواتب الأقسام */
        .dept-bar-item { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
        .dept-bar-item:last-child { margin-bottom: 0; }
        .dept-name { font-size: 13px; font-weight: 600; color: var(--text-dark); min-width: 120px; text-align: left; }
        .dept-bar-bg { flex: 1; height: 28px; background: #f1f5f9; border-radius: 8px; overflow: hidden; position: relative; }
        .dept-bar-fill { height: 100%; border-radius: 8px; display: flex; align-items: center; padding: 0 12px; font-size: 11px; font-weight: 700; color: #fff; transition: width 1s ease; min-width: fit-content; }
        .dept-total { font-size: 13px; font-weight: 700; color: var(--text-dark); min-width: 90px; text-align: left; direction: ltr; font-variant-numeric: tabular-nums; }

        @media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } .charts-grid { grid-template-columns: 1fr; } .dual-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; } .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .kpi-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) { .kpi-grid { grid-template-columns: 1fr; } }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.show { display: block; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; } }
        @media print {
            .sidebar, .topbar, .sidebar-overlay { display: none !important; }
            .main-content { margin-right: 0 !important; }
            .page-body { padding: 0 !important; }
            .kpi-card, .card, .table-card { box-shadow: none !important; break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand"><div class="s-logo"><i class="fas fa-cubes"></i></div><div class="s-name">ERP <span>Pro</span></div></div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">القائمة الرئيسية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/dashboard" class="nav-link"><i class="fas fa-gauge-high"></i><span>لوحة التحكم</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/employee" class="nav-link"><i class="fas fa-users"></i><span>الموظفين</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/product" class="nav-link"><i class="fas fa-boxes-stacked"></i><span>المخزون</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/sale" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/report" class="nav-link active"><i class="fas fa-chart-line"></i><span>التقارير</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">النظام</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/settings" class="nav-link"><i class="fas fa-gear"></i><span>الإعدادات</span></a></div>
        </nav>
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div><div class="su-name"><?php echo $_SESSION['user_name'] ?? ''; ?></div><div class="su-role"><?php echo $_SESSION['user_role'] ?? 'مدير النظام'; ?></div></div>
            <a href="<?php echo URL_ROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="topbar-btn mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $title; ?></div>
                    <div class="breadcrumb"><a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a><i class="fas fa-chevron-left" style="font-size:9px;"></i><span>التقارير والتحليلات</span></div>
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="topbar-btn" title="تصدير PDF" onclick="window.print()"><i class="fas fa-file-pdf"></i></button>
                <button class="topbar-btn" title="طباعة" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">

            <!-- KPI -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fas fa-arrow-trend-up"></i></div>
                    <div class="kpi-label">إجمالي الإيرادات</div>
                    <div class="kpi-value"><?php echo number_format($totalSales, 0); ?></div>
                    <div class="kpi-sub up"><i class="fas fa-coins"></i> ر.س</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fas fa-arrow-trend-down"></i></div>
                    <div class="kpi-label">إجمالي المصروفات</div>
                    <div class="kpi-value"><?php echo number_format($totalExpenses, 0); ?></div>
                    <div class="kpi-sub down"><i class="fas fa-coins"></i> ر.س</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
                    <div class="kpi-label">صافي الربح</div>
                    <div class="kpi-value"><?php echo number_format($netProfit, 0); ?></div>
                    <div class="kpi-sub <?php echo $netProfit >= 0 ? 'up' : 'down'; ?>"><i class="fas fa-coins"></i> ر.س</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="kpi-label">عدد الفواتير</div>
                    <div class="kpi-value"><?php echo $invoiceCount; ?></div>
                    <div class="kpi-sub up"><i class="fas fa-receipt"></i> فاتورة</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fas fa-percent"></i></div>
                    <div class="kpi-label">هامش الربح</div>
                    <div class="kpi-value"><?php echo $profitMargin; ?>%</div>
                    <div class="kpi-sub up"><i class="fas fa-chart-pie"></i> من الإيرادات</div>
                </div>
            </div>

            <!-- شارت الاتجاه + توزيع المصروفات -->
            <div class="charts-grid">
                <div class="card">
                    <div class="card-header"><h3><i class="fas fa-chart-area" style="color:var(--primary);"></i> تطور الأداء الشهري</h3></div>
                    <div class="card-body"><div class="chart-wrap"><canvas id="trendChart"></canvas></div></div>
                </div>
                <div class="card">
                    <div class="card-header"><h3><i class="fas fa-chart-pie" style="color:var(--accent);"></i> توزيع المصروفات</h3></div>
                    <div class="card-body"><div class="chart-wrap"><canvas id="expPieChart"></canvas></div></div>
                </div>
            </div>

            <!-- أفضل المنتجات + تنبيهات المخزون -->
            <div class="dual-grid">
                <div class="table-card">
                    <div class="card-header"><h3><i class="fas fa-trophy" style="color:var(--accent);"></i> أعلى المنتجات مبيعاً</h3></div>
                    <div style="overflow-x:auto;">
                        <table>
                            <thead><tr><th>#</th><th>المنتج</th><th>الوحدات</th><th>الإيرادات</th><th>النسبة</th></tr></thead>
                            <tbody>
                                <?php if (!empty($topProducts)) :
                                    foreach ($topProducts as $i => $p) :
                                        $rc = $i < 3 ? 'rank-' . ($i+1) : 'rank-other';
                                ?>
                                <tr>
                                    <td><span class="rank-badge <?php echo $rc; ?>"><?php echo $i+1; ?></span></td>
                                    <td style="font-weight:600;color:var(--text-dark);"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td style="direction:ltr;text-align:right;font-variant-numeric:tabular-nums;"><?php echo number_format($p['units']); ?></td>
                                    <td class="prod-revenue"><?php echo number_format($p['revenue'], 2); ?> ر.س</td>
                                    <td>
                                        <div class="pct-bar-wrap">
                                            <div class="pct-bar-bg"><div class="pct-bar-fill" style="width:<?php echo $p['pct']; ?>%;"></div></div>
                                            <span class="pct-val"><?php echo $p['pct']; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; else : ?>
                                <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">لا توجد بيانات مبيعات كافية</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3><i class="fas fa-triangle-exclamation" style="color:var(--danger);"></i> تنبيهات المخزون</h3></div>
                    <div class="card-body">
                        <div class="stock-alerts">
                            <?php
                            $hasAlerts = false;
                            foreach ($stockStatus['out_of_stock'] as $item) :
                                $hasAlerts = true;
                            ?>
                            <div class="stock-alert sa-out">
                                <div class="sa-icon"><i class="fas fa-ban"></i></div>
                                <div><div class="sa-name"><?php echo htmlspecialchars($item['name']); ?></div><div class="sa-detail">SKU: <?php echo htmlspecialchars($item['sku']); ?></div></div>
                                <span class="sa-tag">نفذ</span>
                            </div>
                            <?php endforeach; ?>

                            <?php foreach ($stockStatus['low_stock'] as $item) :
                                $hasAlerts = true;
                            ?>
                            <div class="stock-alert sa-low">
                                <div class="sa-icon"><i class="fas fa-exclamation"></i></div>
                                <div><div class="sa-name"><?php echo htmlspecialchars($item['name']); ?></div><div class="sa-detail">SKU: <?php echo htmlspecialchars($item['sku']); ?> — متبقي <?php echo $item['quantity']; ?></div></div>
                                <span class="sa-tag">منخفض</span>
                            </div>
                            <?php endforeach; ?>

                            <?php if (!$hasAlerts) : ?>
                            <div class="stock-empty"><i class="fas fa-check-circle"></i>جميع المنتجات بمخزون كافٍ</div>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;color:var(--text-muted);">قيمة المخزون الإجمالية</span>
                            <span style="font-size:16px;font-weight:800;color:var(--text-dark);font-variant-numeric:tabular-nums;direction:ltr;"><?php echo number_format($stockStatus['total_value'], 2); ?> ر.س</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- رواتب الأقسام -->
            <?php if (!empty($deptSalaries)) : ?>
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-sitemap" style="color:var(--purple);"></i> توزيع الرواتب حسب الأقسام</h3></div>
                <div class="card-body">
                    <?php
                        $maxSalary = max(array_values($deptSalaries));
                        $maxSalary = max($maxSalary, 1);
                        $colorIdx = 0;
                        foreach ($deptSalaries as $dept => $total) :
                            $pct = round(($total / $maxSalary) * 100);
                            $color = $chartColors[$colorIdx % count($chartColors)];
                            $colorIdx++;
                    ?>
                    <div class="dept-bar-item">
                        <div class="dept-name"><?php echo htmlspecialchars($dept); ?></div>
                        <div class="dept-bar-bg">
                            <div class="dept-bar-fill" style="width:<?php echo $pct; ?>%;background:<?php echo $color; ?>;">
                                <?php if ($pct > 20) : ?><?php echo number_format($total, 0); ?> ر.س<?php endif; ?>
                            </div>
                        </div>
                        <div class="dept-total"><?php echo number_format($total, 0); ?> ر.س</div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        /* === بيانات من PHP === */
        const months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
        const ms = <?php echo json_encode($monthlySales); ?>;
        const me = <?php echo json_encode($monthlyExpenses); ?>;
        const mp = <?php echo json_encode($monthlyProfit); ?>;
        const edl = <?php echo json_encode($expDistLabels, JSON_UNESCAPED_UNICODE); ?>;
        const edd = <?php echo json_encode($expDistData); ?>;
        const pieColors = ['#8b5cf6','#06b6d4','#f59e0b','#ef4444','#22c55e','#14b8a6','#ec4899','#f97316','#6366f1','#84cc16'];

        /* === شارت الاتجاه الشهري === */
        (function() {
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        { label: 'الإيرادات', data: ms, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', borderWidth: 2.5, pointRadius: 4, pointBackgroundColor: '#22c55e', pointBorderColor: '#fff', pointBorderWidth: 2, tension: 0.4, fill: true },
                        { label: 'المصروفات', data: me, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.05)', borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#ef4444', pointBorderColor: '#fff', pointBorderWidth: 2, tension: 0.4, fill: true, borderDash: [5,5] },
                        { label: 'صافي الربح', data: mp, borderColor: '#14b8a6', backgroundColor: 'rgba(20,184,166,0.05)', borderWidth: 2.5, pointRadius: 3, pointBackgroundColor: '#14b8a6', pointBorderColor: '#fff', pointBorderWidth: 2, tension: 0.4, fill: false }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', align: 'start', rtl: true, labels: { font: { family: 'Cairo', size: 12, weight: '600' }, color: '#64748b', usePointStyle: true, pointStyle: 'circle', padding: 20 } },
                        tooltip: { rtl: true, titleFont: { family: 'Cairo' }, bodyFont: { family: 'Cairo' }, padding: 12, cornerRadius: 10, callbacks: { label: c => c.dataset.label + ': ' + c.parsed.y.toLocaleString('ar-SA') + ' ر.س' } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8' }, border: { display: false } },
                        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8', callback: v => v >= 1000 ? (v/1000).toFixed(0) + 'K' : v }, border: { display: false } }
                    }
                }
            });
        })();

        /* === شارت توزيع المصروفات === */
        (function() {
            const ctx = document.getElementById('expPieChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: edl,
                    datasets: [{
                        data: edd,
                        backgroundColor: pieColors.slice(0, edl.length),
                        borderWidth: 0, hoverOffset: 10, borderRadius: 4, spacing: 3
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '60%',
                    plugins: {
                        legend: { position: 'bottom', rtl: true, labels: { font: { family: 'Cairo', size: 11, weight: '600' }, color: '#64748b', usePointStyle: true, pointStyle: 'rectRounded', padding: 14 } },
                        tooltip: { rtl: true, titleFont: { family: 'Cairo' }, bodyFont: { family: 'Cairo' }, padding: 12, cornerRadius: 10, callbacks: { label: c => c.label + ': ' + c.parsed + '%' } }
                    }
                }
            });
        })();

        /* === موبايل === */
        const sidebar = document.getElementById('sidebar'); const overlay = document.getElementById('sidebarOverlay'); const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>