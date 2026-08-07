<?php
// app/views/account/trial_balance.php
$pageTitle = $data['title'] ?? 'ميزان المراجعة';
$balances = $data['balances'] ?? [];
$totalDebit = $data['totalDebit'] ?? 0;
$totalCredit = $data['totalCredit'] ?? 0;
$flash = $data['flash'] ?? null;
$currentUrl = 'account/trial-balance';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text"><span class="s-name">ERP <span>Pro</span></span></div>
        </div>
        <?php if(class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'مدير النظام'); ?></div>
                <div class="su-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'admin'); ?></div>
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
                        <span>ميزان المراجعة</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <button class="topbar-btn" title="طباعة التقرير" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">
            
            <div class="report-header">
                <h1 class="report-title">ميزان المراجعة بالأرصدة (Trial Balance)</h1>
                <p class="report-date">الفترة المالية حتى تاريخ: <strong dir="ltr"><?php echo date('Y-m-d'); ?></strong></p>
            </div>

            <div class="table-card">
                <div class="tc-header">
                    <h3><i class="fas fa-scale-unbalanced"></i> الحسابات النشطة ذات الرصيد</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>اسم الحساب المحاسبي</th>
                                <th class="num-col">أرصدة مدينة (Debit)</th>
                                <th class="num-col">أرصدة دائنة (Credit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($balances as $b) : ?>
                            <tr>
                                <td>
                                    <span class="acc-code"><?php echo $b['code']; ?></span>
                                    <span class="acc-name"><?php echo htmlspecialchars($b['name']); ?></span>
                                </td>
                                <td class="num-col">
                                    <?php if ($b['debit'] > 0) : ?>
                                        <span class="val-num debit"><?php echo number_format($b['debit'], 2); ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--border);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="num-col">
                                    <?php if ($b['credit'] > 0) : ?>
                                        <span class="val-num credit"><?php echo number_format($b['credit'], 2); ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--border);">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($balances)): ?>
                                <tr><td colspan="3" style="text-align:center; padding: 40px; color:var(--text-muted);">لا توجد حركات محاسبية بعد</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>المجموع الكلي:</td>
                                <td class="num-col debit-total"><?php echo number_format($totalDebit, 2); ?></td>
                                <td class="num-col credit-total"><?php echo number_format($totalCredit, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <?php 
                $diff = round(abs($totalDebit - $totalCredit), 2);
                if ($totalDebit == 0 && $totalCredit == 0) :
            ?>
                <!-- لا يوجد رسالة إذا كان فارغاً -->
            <?php elseif ($diff == 0) : ?>
                <div class="balance-check is-balanced">
                    <i class="fas fa-check-circle"></i> ميزان المراجعة متطابق
                </div>
            <?php else : ?>
                <div class="balance-check not-balanced">
                    <i class="fas fa-triangle-exclamation"></i> يوجد فارق بقيمة (<?php echo number_format($diff, 2); ?> ر.س) - يجب مراجعة القيود!
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