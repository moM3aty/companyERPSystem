<?php
// app/views/account/balance_sheet.php
$pageTitle = $data['title'] ?? 'الميزانية العمومية';
$accounts = $data['accounts'] ?? [];
$balances = $data['balances'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'account/balance-sheet';

// حساب المجاميع
$totalAssets = 0;
$totalLiabilities = 0;
$totalEquity = 0;

$assetsList = [];
$liabEquityList = [];

foreach ($accounts as $acc) {
    $balance = $balances[$acc->id] ?? 0;
    
    // الأصول (طبيعتها مدينة)
    if ($acc->type == 'asset') {
        $totalAssets += $balance;
        $assetsList[] = ['code' => $acc->code, 'name' => $acc->name, 'balance' => $balance];
    } 
    // الخصوم وحقوق الملكية (طبيعتها دائنة - عادة تخزن كقيم سالبة في دفتر الأستاذ حسب تصميمك المالي، 
    // سنفترض هنا أن الدالة getAccountBalance تعيد الصافي: المدين - الدائن، 
    // لذا للأصول يكون الموجب مدين، وللخصوم الموجب يعني دائن إذا قمنا بعكس الإشارة أو حسب طريقة التخزين)
    // لتصحيح العرض إذا كانت الدالة ترجع (المدين - الدائن)، الخصوم يجب أن تكون (الدائن - المدين)
    // سأقوم بعكس الإشارة للخصوم والملكية لتظهر كموجب في التقرير.
    elseif ($acc->type == 'liability') {
        $bal = -$balance; 
        $totalLiabilities += $bal;
        $liabEquityList[] = ['code' => $acc->code, 'name' => $acc->name, 'balance' => $bal, 'type' => 'liability'];
    } 
    elseif ($acc->type == 'equity') {
        $bal = -$balance;
        $totalEquity += $bal;
        $liabEquityList[] = ['code' => $acc->code, 'name' => $acc->name, 'balance' => $bal, 'type' => 'equity'];
    }
}
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
                        <span>الميزانية العمومية</span>
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
                <h1 class="report-title">الميزانية العمومية</h1>
                <p class="report-date">كما في تاريخ: <strong dir="ltr"><?php echo date('Y-m-d'); ?></strong></p>
            </div>

            <div class="report-grid">
                
                <!-- عمود الأصول -->
                <div class="report-card">
                    <div class="rc-header">
                        <i class="fas fa-building-columns"></i>
                        <h3>الأصول (الموجودات)</h3>
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
                                <?php foreach($assetsList as $asset): ?>
                                <tr>
                                    <td>
                                        <span class="acc-code"><?php echo $asset['code']; ?></span>
                                        <span class="acc-name"><?php echo htmlspecialchars($asset['name']); ?></span>
                                    </td>
                                    <td class="acc-bal"><?php echo number_format($asset['balance'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($assetsList)): ?>
                                    <tr><td colspan="2" style="text-align:center; color:var(--text-muted); padding: 30px;">لا توجد حسابات أصول</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="rc-footer">
                        <span class="rc-total-label">إجمالي الأصول</span>
                        <span class="rc-total-val"><?php echo number_format($totalAssets, 2); ?> <span style="font-size:12px;">ر.س</span></span>
                    </div>
                </div>

                <!-- عمود الخصوم وحقوق الملكية -->
                <div class="report-card">
                    <div class="rc-header liabilities">
                        <i class="fas fa-scale-unbalanced"></i>
                        <h3>الخصوم وحقوق الملكية</h3>
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
                                <?php 
                                    $hasLiab = false;
                                    foreach($liabEquityList as $item): 
                                        $hasLiab = true;
                                        $icon = $item['type'] == 'equity' ? '<i class="fas fa-certificate" style="color:var(--accent);font-size:10px;margin-left:4px;" title="حقوق ملكية"></i>' : '';
                                ?>
                                <tr>
                                    <td>
                                        <span class="acc-code"><?php echo $item['code']; ?></span>
                                        <span class="acc-name"><?php echo $icon . htmlspecialchars($item['name']); ?></span>
                                    </td>
                                    <td class="acc-bal"><?php echo number_format($item['balance'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(!$hasLiab): ?>
                                    <tr><td colspan="2" style="text-align:center; color:var(--text-muted); padding: 30px;">لا توجد حسابات خصوم/ملكية</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="rc-footer">
                        <span class="rc-total-label">إجمالي الخصوم والملكية</span>
                        <span class="rc-total-val danger"><?php echo number_format($totalLiabilities + $totalEquity, 2); ?> <span style="font-size:12px;">ر.س</span></span>
                    </div>
                </div>

                <!-- المعادلة المحاسبية -->
                <div class="equation-box">
                    <div class="eq-item">
                        <span class="eq-label">الأصول</span>
                        <span class="eq-val" style="color:var(--primary-dark);"><?php echo number_format($totalAssets, 2); ?></span>
                    </div>
                    <div class="eq-operator">=</div>
                    <div class="eq-item">
                        <span class="eq-label">الخصوم</span>
                        <span class="eq-val" style="color:var(--danger);"><?php echo number_format($totalLiabilities, 2); ?></span>
                    </div>
                    <div class="eq-operator">+</div>
                    <div class="eq-item">
                        <span class="eq-label">حقوق الملكية</span>
                        <span class="eq-val" style="color:var(--accent);"><?php echo number_format($totalEquity, 2); ?></span>
                    </div>
                </div>

            </div>

            <?php 
                $difference = round(abs($totalAssets - ($totalLiabilities + $totalEquity)), 2);
                if ($difference == 0) : 
            ?>
                <div class="balance-check is-balanced">
                    <i class="fas fa-check-circle"></i> الميزانية متوازنة تماماً
                </div>
            <?php else : ?>
                <div class="balance-check not-balanced">
                    <i class="fas fa-triangle-exclamation"></i> الميزانية غير متوازنة (فارق: <?php echo number_format($difference, 2); ?> ر.س) - يرجى مراجعة القيود المحاسبية.
                </div>
            <?php endif; ?>

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