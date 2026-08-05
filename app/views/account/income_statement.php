<?php
// app/views/account/income_statement.php
$pageTitle = $data['title'] ?? 'قائمة الدخل';
$accounts = $data['accounts'] ?? [];
$balances = $data['balances'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'account/income-statement';

// حساب المجاميع
$totalRevenue = 0;
$totalExpense = 0;

$revenueList = [];
$expenseList = [];

foreach ($accounts as $acc) {
    $balance = $balances[$acc->id] ?? 0;
    
    if ($acc->type == 'revenue') {
        // الإيرادات طبيعتها دائنة (سالبة في الدفتر عادة)، نعكس الإشارة لعرضها كموجب
        $bal = -$balance; 
        $totalRevenue += $bal;
        $revenueList[] = ['code' => $acc->code, 'name' => $acc->name, 'balance' => $bal];
    } elseif ($acc->type == 'expense') {
        // المصروفات طبيعتها مدينة
        $totalExpense += $balance;
        $expenseList[] = ['code' => $acc->code, 'name' => $acc->name, 'balance' => $balance];
    }
}

$netProfit = $totalRevenue - $totalExpense;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: var(--radius-sm); color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s ease; position: relative; }
        .nav-link i { width: 20px; text-align: center; font-size: 15px; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }
        .nav-link.active { background: rgba(20, 184, 166, 0.1); color: var(--primary); font-weight: 600; }
        .nav-link.active::before { content: ''; position: absolute; right: -12px; top: 50%; transform: translateY(-50%); width: 3px; height: 24px; background: var(--primary); border-radius: 0 4px 4px 0; }
        
        .sidebar-user { padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; align-items: center; gap: 12px; }
        .sidebar-user .su-avatar { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .sidebar-user .su-info { flex: 1; min-width: 0; }
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; }
        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239, 68, 68, 0.1); }

        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; transition: margin 0.3s ease; }
        .topbar { height: var(--topbar-h); background: var(--card-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .page-title { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .breadcrumb a:hover { color: var(--primary); }
        .topbar-left { display: flex; align-items: center; gap: 8px; }
        .topbar-btn { width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 15px; }
        .topbar-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }
        .mobile-menu-btn { display: none; }
        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        
        .flash-msg { padding: 14px 20px; border-radius: var(--radius-sm); display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; margin-bottom: 24px; border: 1px solid transparent; }
        .flash-msg.flash-error { background: var(--danger-light); color: #dc2626; border-color: #fecaca; }

        .report-header { text-align: center; margin-bottom: 32px; animation: fadeUp 0.5s ease both; }
        .report-title { font-size: 24px; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; }
        .report-date { font-size: 14px; color: var(--text-muted); }

        .report-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; animation: fadeUp 0.5s ease 0.1s both; }

        .report-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; display: flex; flex-direction: column; }
        
        .rc-header { padding: 16px 24px; border-bottom: 2px solid var(--success); background: #f8fafc; display: flex; align-items: center; gap: 10px; }
        .rc-header.expenses { border-color: var(--danger); }
        
        .rc-header h3 { font-size: 16px; font-weight: 700; color: var(--text-dark); }
        .rc-header i { font-size: 18px; color: var(--success); }
        .rc-header.expenses i { color: var(--danger); }

        .rc-body { flex: 1; padding: 0; }

        .report-table { width: 100%; border-collapse: collapse; }
        .report-table th { padding: 12px 24px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; background: #fff; border-bottom: 1px solid var(--border); text-align: right; }
        .report-table td { padding: 12px 24px; font-size: 14px; color: var(--text-body); border-bottom: 1px dashed var(--border); }
        .report-table tbody tr:last-child td { border-bottom: none; }
        .report-table tbody tr:hover { background: rgba(0,0,0,0.01); }
        
        .acc-code { font-family: monospace; font-size: 12px; color: var(--text-muted); margin-left: 8px; }
        .acc-name { font-weight: 600; color: var(--text-dark); }
        .acc-bal { font-weight: 700; font-variant-numeric: tabular-nums; direction: ltr; text-align: left; }
        
        .rc-footer { padding: 16px 24px; background: #f8fafc; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .rc-total-label { font-size: 15px; font-weight: 800; color: var(--text-dark); }
        .rc-total-val { font-size: 18px; font-weight: 800; direction: ltr; font-variant-numeric: tabular-nums; }
        .rc-total-val.success { color: var(--success); }
        .rc-total-val.danger { color: var(--danger); }

        .net-profit-card { grid-column: 1 / -1; border-radius: var(--radius); padding: 32px; text-align: center; margin-top: 8px; box-shadow: var(--shadow-sm); animation: fadeUp 0.5s ease 0.2s both; position: relative; overflow: hidden; }
        
        .net-profit-card.is-profit { background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: #fff; border: none; }
        .net-profit-card.is-loss { background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 100%); color: #fff; border: none; }
        .net-profit-card.is-zero { background: var(--card-bg); border: 1px solid var(--border); color: var(--text-dark); }
        
        .net-profit-card::before { content: ''; position: absolute; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%; top: -150px; left: -100px; pointer-events: none; }
        .net-profit-card::after { content: ''; position: absolute; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%; bottom: -100px; right: -50px; pointer-events: none; }
        
        .np-label { font-size: 16px; font-weight: 700; opacity: 0.9; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; position: relative; z-index: 2; }
        .np-value { font-size: 42px; font-weight: 900; direction: ltr; font-variant-numeric: tabular-nums; position: relative; z-index: 2; letter-spacing: -1px; }
        .np-value small { font-size: 18px; font-weight: 600; opacity: 0.8; margin-left: 6px; }

        @media (max-width: 992px) { .report-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer; }
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
        }
        
        @media print {
            .sidebar, .topbar, .sidebar-overlay { display: none !important; }
            .main-content { margin-right: 0 !important; }
            .page-body { padding: 0 !important; }
            .report-card, .net-profit-card { box-shadow: none !important; border: 1px solid #000 !important; color: #000 !important; background: #fff !important; }
            body { background: #fff !important; }
            .np-value, .np-label { color: #000 !important; }
        }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; backdrop-filter: blur(2px); }
        .sidebar-overlay.show { display: block; }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text">
                <span class="s-name">ERP <span>Pro</span></span>
            </div>
        </div>
        <?php if(class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo $_SESSION['user_name'] ?? 'مدير النظام'; ?></div>
                <div class="su-role"><?php echo $_SESSION['user_role'] ?? 'admin'; ?></div>
            </div>
            <a href="<?php echo URL_ROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $pageTitle; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>المحاسبة</span>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>قائمة الدخل</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <button class="topbar-btn" title="طباعة التقرير" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">
            
            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-circle-xmark"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="report-header">
                <h1 class="report-title">قائمة الدخل (الأرباح والخسائر)</h1>
                <p class="report-date">الفترة المالية حتى: <strong dir="ltr"><?php echo date('Y-m-d'); ?></strong></p>
            </div>

            <div class="report-grid">
                
                <!-- الإيرادات -->
                <div class="report-card">
                    <div class="rc-header">
                        <i class="fas fa-arrow-trend-up"></i>
                        <h3>الإيرادات (المبيعات)</h3>
                    </div>
                    <div class="rc-body">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>الحساب</th>
                                    <th style="text-align:left;">الرصيد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($revenueList as $rev): ?>
                                <tr>
                                    <td>
                                        <span class="acc-code"><?php echo $rev['code']; ?></span>
                                        <span class="acc-name"><?php echo htmlspecialchars($rev['name']); ?></span>
                                    </td>
                                    <td class="acc-bal"><?php echo number_format($rev['balance'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($revenueList)): ?>
                                    <tr><td colspan="2" style="text-align:center; color:var(--text-muted); padding: 30px;">لا توجد إيرادات مسجلة</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="rc-footer">
                        <span class="rc-total-label">إجمالي الإيرادات</span>
                        <span class="rc-total-val success"><?php echo number_format($totalRevenue, 2); ?> <span style="font-size:12px;">ر.س</span></span>
                    </div>
                </div>

                <!-- المصروفات -->
                <div class="report-card">
                    <div class="rc-header expenses">
                        <i class="fas fa-arrow-trend-down"></i>
                        <h3>المصروفات والتكاليف</h3>
                    </div>
                    <div class="rc-body">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>الحساب</th>
                                    <th style="text-align:left;">الرصيد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($expenseList as $exp): ?>
                                <tr>
                                    <td>
                                        <span class="acc-code"><?php echo $exp['code']; ?></span>
                                        <span class="acc-name"><?php echo htmlspecialchars($exp['name']); ?></span>
                                    </td>
                                    <td class="acc-bal"><?php echo number_format($exp['balance'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($expenseList)): ?>
                                    <tr><td colspan="2" style="text-align:center; color:var(--text-muted); padding: 30px;">لا توجد مصروفات مسجلة</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="rc-footer">
                        <span class="rc-total-label">إجمالي المصروفات</span>
                        <span class="rc-total-val danger"><?php echo number_format($totalExpense, 2); ?> <span style="font-size:12px;">ر.س</span></span>
                    </div>
                </div>

                <!-- صافي الربح / الخسارة -->
                <?php 
                    $npClass = 'is-zero';
                    $npLabel = 'صافي الربح / (الخسارة)';
                    if ($netProfit > 0) {
                        $npClass = 'is-profit';
                        $npLabel = 'صافي الربح (فائض)';
                    } elseif ($netProfit < 0) {
                        $npClass = 'is-loss';
                        $npLabel = 'صافي الخسارة (عجز)';
                    }
                ?>
                <div class="net-profit-card <?php echo $npClass; ?>">
                    <div class="np-label"><?php echo $npLabel; ?></div>
                    <div class="np-value"><?php echo number_format(abs($netProfit), 2); ?><small>ر.س</small></div>
                </div>

            </div>

        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>