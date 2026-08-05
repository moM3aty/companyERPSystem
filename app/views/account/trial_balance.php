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
        .sidebar-brand .s-logo { width: 42px; height: 42px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; flex-shrink: 0; box-shadow: 0 4px 15px rgba(20,184,166,0.25); }
        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: var(--radius-sm); color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s ease; position: relative; }
        .nav-link i { width: 20px; text-align: center; font-size: 15px; transition: color 0.2s; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }
        .nav-link.active { background: rgba(20, 184, 166, 0.1); color: var(--primary); font-weight: 600; }
        .nav-link.active::before { content: ''; position: absolute; right: -12px; top: 50%; transform: translateY(-50%); width: 3px; height: 24px; background: var(--primary); border-radius: 0 4px 4px 0; }
        .sidebar-user { padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; align-items: center; gap: 12px; }
        .sidebar-user .su-avatar { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .sidebar-user .su-info { flex: 1; min-width: 0; }
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto;}
        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239, 68, 68, 0.1); }

        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; transition: margin 0.3s ease; }
        .topbar { height: var(--topbar-h); background: var(--card-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .page-title { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .breadcrumb a:hover { color: var(--primary); }
        .topbar-btn { width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 15px; }
        .topbar-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }
        .mobile-menu-btn { display: none; }
        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        .report-header { text-align: center; margin-bottom: 32px; animation: fadeUp 0.5s ease both; }
        .report-title { font-size: 24px; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; }
        .report-date { font-size: 14px; color: var(--text-muted); }

        .table-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-md); overflow: hidden; animation: fadeUp 0.5s ease 0.1s both; max-width: 900px; margin: 0 auto;}
        
        .tc-header { padding: 20px 24px; border-bottom: 2px solid var(--primary); display: flex; align-items: center; justify-content: space-between; background: #f8fafc; }
        .tc-header h3 { font-size: 16px; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 8px; margin: 0;}
        .tc-header h3 i { color: var(--primary); font-size: 18px;}

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 14px 24px; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; background: #fff; border-bottom: 1.5px solid var(--border); text-align: right; }
        thead th.num-col { text-align: left; }
        tbody tr { transition: background 0.15s; border-bottom: 1px dashed var(--border); }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(0,0,0,0.01); }
        tbody td { padding: 14px 24px; font-size: 14px; color: var(--text-body); vertical-align: middle;}
        
        .acc-code { font-family: monospace; font-size: 12px; color: var(--text-muted); margin-left: 10px; }
        .acc-name { font-weight: 700; color: var(--text-dark); }
        
        .val-num { font-weight: 700; font-variant-numeric: tabular-nums; direction: ltr; display: inline-block;}
        .val-num.debit { color: var(--info-dark); }
        .val-num.credit { color: var(--purple-dark); }

        tfoot td { padding: 18px 24px; font-weight: 800; font-size: 16px; background: #f8fafc; border-top: 2px solid var(--border); }
        tfoot td.num-col { text-align: left; font-variant-numeric: tabular-nums; direction: ltr; }
        tfoot td.debit-total { color: var(--info-dark); }
        tfoot td.credit-total { color: var(--purple-dark); }

        .balance-check { margin-top: 24px; padding: 16px; border-radius: var(--radius-sm); font-size: 15px; font-weight: 700; display: flex; justify-content: center; align-items: center; gap: 10px; max-width: 900px; margin-left: auto; margin-right: auto; animation: fadeUp 0.5s ease 0.2s both;}
        .is-balanced { background: var(--success-light); color: var(--success); border: 1px solid #bbf7d0; }
        .not-balanced { background: var(--danger-light); color: var(--danger); border: 1px solid #fecaca; }

        @media print {
            .sidebar, .topbar, .sidebar-overlay { display: none !important; }
            .main-content { margin-right: 0 !important; }
            .page-body { padding: 0 !important; }
            .table-card { box-shadow: none !important; border-color: #000 !important; max-width: 100% !important; margin: 0; }
            body { background: #fff !important; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer; }
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; backdrop-filter: blur(2px);}
        .sidebar-overlay.show { display: block; }
    </style>
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