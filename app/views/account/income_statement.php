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
            <a href="<?php echo URLROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $pageTitle; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URLROOT; ?>/dashboard">الرئيسية</a>
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