<?php
// app/views/account/view_journal.php
$pageTitle = $data['title'] ?? 'تفاصيل القيد اليومي';
$entry = $data['entry'] ?? null;
$lines = $data['lines'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'account/ledger';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>القيد رقم: <?php echo htmlspecialchars($entry->entry_number); ?></title>
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
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto; }
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
        
        .print-actions { display: flex; gap: 10px; align-items: center; }
        .btn-print { display: inline-flex; align-items: center; gap: 8px; padding: 9px 20px; background: linear-gradient(135deg, var(--purple), #7c3aed); color: #fff; border: none; border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.2); }
        .btn-print:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(139, 92, 246, 0.3); }
        .btn-back { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; background: transparent; color: var(--text-body); border: 1px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-back:hover { background: var(--page-bg); }

        .journal-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-md); overflow: hidden; animation: fadeUp 0.5s ease both; max-width: 900px; margin: 0 auto; }

        .jc-header { padding: 32px 40px; border-bottom: 2px solid var(--primary); display: flex; justify-content: space-between; align-items: flex-start; background: #fafafa; flex-wrap: wrap; gap: 20px;}
        .jc-title-area { display: flex; align-items: center; gap: 16px; }
        .jc-icon { width: 56px; height: 56px; border-radius: 14px; background: var(--primary-light); color: var(--primary-dark); display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .jc-number { font-size: 22px; font-weight: 800; color: var(--text-dark); margin-bottom: 4px; font-family: monospace; letter-spacing: 1px;}
        .jc-desc { font-size: 14px; color: var(--text-body); font-weight: 600; }
        
        .jc-meta { text-align: left; }
        .jc-meta-item { display: flex; justify-content: flex-end; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; }
        .jc-meta-item strong { color: var(--text-dark); }
        .ref-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; background: var(--info-light); color: var(--info); border: 1px solid rgba(6,182,212,0.2); }

        .jc-table-wrap { padding: 0 40px; overflow-x: auto;}
        .jc-table { width: 100%; border-collapse: collapse; margin: 24px 0; min-width: 600px;}
        .jc-table thead th { padding: 14px 16px; font-size: 12px; font-weight: 700; color: var(--text-muted); background: #f8fafc; border-bottom: 2px solid var(--border); text-align: right; border-top: 1px solid var(--border); }
        .jc-table thead th:last-child, .jc-table thead th:nth-last-child(2) { text-align: center; }
        .jc-table tbody td { padding: 16px; font-size: 14px; color: var(--text-body); border-bottom: 1px solid var(--border); }
        .acc-cell { font-weight: 700; color: var(--text-dark); }
        .acc-code { font-family: monospace; font-size: 12px; color: var(--text-muted); margin-left: 8px; font-weight: normal;}
        
        .jc-table tbody td:nth-last-child(2) { text-align: center; font-variant-numeric: tabular-nums; direction: ltr; font-weight: 700; color: var(--info); }
        .jc-table tbody td:last-child { text-align: center; font-variant-numeric: tabular-nums; direction: ltr; font-weight: 700; color: var(--purple); }

        .jc-table tfoot td { padding: 16px; font-weight: 800; font-size: 16px; background: #f8fafc; border-top: 2px solid var(--border); }
        .jc-table tfoot td:first-child { text-align: left; color: var(--text-muted); }
        .jc-table tfoot td:nth-child(2) { text-align: center; color: var(--info); direction: ltr; }
        .jc-table tfoot td:last-child { text-align: center; color: var(--purple); direction: ltr; }

        .jc-footer { padding: 20px 40px; background: #f8fafc; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--text-muted); }
        .jc-footer-stamp { display: flex; align-items: center; gap: 8px; }
        .jc-footer-stamp i { color: var(--primary); font-size: 14px; }

        @media print {
            .sidebar, .topbar, .sidebar-overlay, .flash-msg { display: none !important; }
            .main-content { margin-right: 0 !important; }
            .page-body { padding: 0 !important; background: #fff !important; }
            .journal-card { box-shadow: none !important; border: none !important; border-radius: 0 !important; max-width: 100% !important; margin:0;}
            body { background: #fff !important; }
            .jc-header { border-bottom: 2px solid #000; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; } .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer;}
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .jc-header { padding: 24px 20px; flex-direction: column; align-items: flex-start; }
            .jc-meta { text-align: right; width: 100%; border-top: 1px dashed var(--border); padding-top: 16px; margin-top: 8px;}
            .jc-meta-item { justify-content: flex-start; }
            .jc-table-wrap { padding: 0 20px; }
            .jc-footer { padding: 16px 20px; flex-direction: column; gap: 8px; text-align: center;}
            .print-actions { display:none;}
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
                        <a href="<?php echo URLROOT; ?>/account/ledger">دفتر الأستاذ</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>عرض القيد</span>
                    </div>
                </div>
            </div>
            <div class="print-actions">
                <a href="<?php echo URLROOT; ?>/account/ledger" class="btn-back"><i class="fas fa-arrow-right"></i> السجل</a>
                <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> طباعة القيد</button>
            </div>
        </header>

        <div class="page-body">
            
            <div class="journal-card">
                <div class="jc-header">
                    <div class="jc-title-area">
                        <div class="jc-icon"><i class="fas fa-book-open"></i></div>
                        <div>
                            <div class="jc-number">قيد #<?php echo htmlspecialchars($entry->entry_number); ?></div>
                            <div class="jc-desc"><?php echo htmlspecialchars($entry->description ?? 'بدون بيان'); ?></div>
                        </div>
                    </div>
                    <div class="jc-meta">
                        <div class="jc-meta-item">
                            تاريخ السريان: <strong><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></strong>
                        </div>
                        <div class="jc-meta-item">
                            بواسطة: <strong><i class="fas fa-user-gear"></i> <?php echo htmlspecialchars($entry->created_by_name ?? 'النظام'); ?></strong>
                        </div>
                        <?php if ($entry->reference_type): ?>
                        <div class="jc-meta-item" style="margin-top: 10px;">
                            <span class="ref-badge"><i class="fas fa-link"></i> مرجع: <?php echo htmlspecialchars($entry->reference_type); ?> (#<?php echo $entry->reference_id; ?>)</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="jc-table-wrap">
                    <table class="jc-table">
                        <thead>
                            <tr>
                                <th>اسم الحساب المحاسبي</th>
                                <th>البيان الفرعي</th>
                                <th style="width: 140px;">مدين (Debit)</th>
                                <th style="width: 140px;">دائن (Credit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $totalDebit = 0;
                                $totalCredit = 0;
                                foreach($lines as $line) : 
                                    $totalDebit += $line->debit;
                                    $totalCredit += $line->credit;
                            ?>
                            <tr>
                                <td>
                                    <span class="acc-code"><?php echo htmlspecialchars($line->account_code); ?></span>
                                    <span class="acc-cell"><?php echo htmlspecialchars($line->account_name); ?></span>
                                </td>
                                <td><span style="font-size:12px;color:var(--text-muted);"><?php echo htmlspecialchars($line->description ?? '—'); ?></span></td>
                                <td><?php echo $line->debit > 0 ? number_format($line->debit, 2) : '—'; ?></td>
                                <td><?php echo $line->credit > 0 ? number_format($line->credit, 2) : '—'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">إجمالي القيد المزدوج:</td>
                                <td><?php echo number_format($totalDebit, 2); ?></td>
                                <td><?php echo number_format($totalCredit, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="jc-footer">
                    <div class="jc-footer-stamp">
                        <i class="fas fa-scale-balanced"></i>
                        <span>قيد محاسبي معتمد في دفتر الأستاذ العام.</span>
                    </div>
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