<?php
// app/views/sales/index.php
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
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

        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; animation: fadeUp 0.5s ease both; }
        .summary-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); padding: 22px; display: flex; align-items: center; gap: 16px; transition: all 0.3s; }
        .summary-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .sc-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0; }
        .summary-card:nth-child(1) .sc-icon { background: var(--primary-light); color: var(--primary-dark); }
        .summary-card:nth-child(2) .sc-icon { background: var(--accent-light); color: var(--accent); }
        .summary-card:nth-child(3) .sc-icon { background: var(--success-light); color: var(--success); }
        .sc-label { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-bottom: 2px; }
        .sc-value { font-size: 24px; font-weight: 800; color: var(--text-dark); font-variant-numeric: tabular-nums; }
        .sc-unit { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-right: 2px; }

        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; animation: fadeUp 0.5s ease 0.1s both; }
        .toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .search-box { position: relative; }
        .search-box input { width: 260px; padding: 10px 16px 10px 40px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 13px; background: var(--card-bg); color: var(--text-dark); outline: none; transition: all 0.2s; }
        .search-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,184,166,0.08); }
        .search-box input::placeholder { color: var(--text-muted); }
        .search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px; }
        .btn-add { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; border: none; border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.25s; box-shadow: 0 2px 10px rgba(20,184,166,0.2); }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(20,184,166,0.3); }
        .result-count { font-size: 13px; color: var(--text-muted); }
        .result-count strong { color: var(--text-dark); font-weight: 700; }

        .table-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.15s both; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 14px 20px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; background: #f8fafc; border-bottom: 1.5px solid var(--border); text-align: right; white-space: nowrap; }
        tbody tr { transition: background 0.15s; border-bottom: 1px solid var(--border); }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(20,184,166,0.02); }
        tbody td { padding: 14px 20px; font-size: 13.5px; }

        .inv-num { font-family: monospace; direction: ltr; font-size: 12px; color: var(--primary-dark); font-weight: 600; background: var(--primary-light); padding: 3px 10px; border-radius: 6px; white-space: nowrap; display: inline-block; }

        .cust-link { color: var(--text-dark); text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .cust-link:hover { color: var(--primary); }

        .inv-amount { font-weight: 700; color: var(--text-dark); font-variant-numeric: tabular-nums; direction: ltr; display: inline-block; }
        .inv-amount .curr { font-size: 11px; font-weight: 500; color: var(--text-muted); margin-right: 2px; }

        .inv-date { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
        .inv-date i { font-size: 11px; }

        .inv-status { display: inline-flex; align-items: center; gap: 5px; padding: 3px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .inv-status.st-paid { background: var(--success-light); color: #15803d; }
        .inv-status.st-unpaid { background: var(--accent-light); color: #b45309; }

        .actions-cell { display: flex; align-items: center; gap: 6px; }
        .act-btn { width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 13px; transition: all 0.2s; text-decoration: none; color: var(--text-body); }
        .act-btn.btn-view { color: var(--primary); }
        .act-btn.btn-view:hover { background: var(--primary-light); border-color: var(--primary); }
        .act-btn.btn-print { color: var(--text-muted); }
        .act-btn.btn-print:hover { background: var(--page-bg); border-color: var(--text-muted); }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 48px; color: var(--border); margin-bottom: 16px; display: block; }
        .empty-state h4 { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .empty-state p { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; } .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .search-box input { width: 200px; }
            .toolbar { flex-direction: column; align-items: flex-start; }
            .summary-grid { grid-template-columns: 1fr; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.show { display: block; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; } }
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
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/sale" class="nav-link active"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">العلاقات</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/customer" class="nav-link"><i class="fas fa-address-book"></i><span>العملاء</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/supplier" class="nav-link"><i class="fas fa-truck-field"></i><span>الموردين</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/report" class="nav-link"><i class="fas fa-chart-line"></i><span>التقارير</span></a></div>
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
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $data['title']; ?></div>
                    <div class="breadcrumb"><a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a><i class="fas fa-chevron-left" style="font-size:9px;"></i><span>الفواتير والمبيعات</span></div>
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="topbar-btn" title="تصدير Excel" aria-label="تصدير"><i class="fas fa-file-excel"></i></button>
                <button class="topbar-btn" title="طباعة" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">

            <!-- ملخص -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="sc-label">إجمالي الفواتير</div>
                        <div class="sc-value"><?php echo count($data['invoices']); ?></div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-coins"></i></div>
                    <div>
                        <div class="sc-label">إجمالي المبيعات</div>
                        <div class="sc-value"><?php
                            $total = 0;
                            foreach($data['invoices'] as $inv) $total += $inv->total_amount;
                            echo number_format($total, 0);
                        ?> <span class="sc-unit">ر.س</span></div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <div class="sc-label">متوسط قيمة الفاتورة</div>
                        <div class="sc-value"><?php
                            $total = 0;
                            foreach($data['invoices'] as $inv) $total += $inv->total_amount;
                            $avg = count($data['invoices']) > 0 ? $total / count($data['invoices']) : 0;
                            echo number_format($avg,  0);
                        ?> <span class="sc-unit">ر.س</span></div>
                    </div>
                </div>
            </div>

            <!-- شريط الأدوات -->
            <div class="toolbar">
                <div class="toolbar-right">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="ابحث برقم الفاتورة أو اسم العميل..." autocomplete="off">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:14px;">
                    <span class="result-count">عرض <strong id="visibleCount"><?php echo count($data['invoices']); ?></strong> فاتورة</span>
                    <a href="<?php echo URL_ROOT; ?>/sale/create" class="btn-add"><i class="fas fa-plus"></i> فاتورة جديدة</a>
                </div>
            </div>

            <!-- الجدول -->
            <div class="table-card">
                <div class="table-wrap">
                    <table id="invTable">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>العميل</th>
                                <th>الإجمالي</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="invBody">
                            <?php foreach($data['invoices'] as $inv) :
                                $isPaid = isset($inv->payment_status) && $inv->payment_status === 'paid';
                            ?>
                            <tr class="inv-row" data-search="<?php echo htmlspecialchars($inv->invoice_number . ' ' . $inv->customer_name); ?>">
                                <td><span class="inv-num"><?php echo htmlspecialchars($inv->invoice_number); ?></span></td>
                                <td>
                                    <?php if (!empty($inv->customer_id)) : ?>
                                        <a href="<?php echo URL_ROOT; ?>/customer/view/<?php echo $inv->customer_id; ?>" class="cust-link"><?php echo htmlspecialchars($inv->customer_name); ?></a>
                                    <?php else : ?>
                                        <span style="color:var(--text-muted);">— عميل نقدي — <?php echo htmlspecialchars($inv->name ?? $inv->customer_name); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="inv-amount"><?php echo number_format($inv->total_amount, 2); ?><span class="curr">ر.س</span></span></td>
                                <td>
                                    <span class="inv-date"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($inv->created_at)); ?></span>
                                </td>
                                <td>
                                    <?php if ($isPaid) : ?>
                                        <span class="inv-status st-paid"><i class="fas fa-circle-check"></i> مدفوعة</span>
                                    <?php else : ?>
                                        <span class="inv-status st-unpaid"><i class="fas fa-clock"></i> غير مدفوعة</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="<?php echo URL_ROOT; ?>/sale/view/<?php echo $inv->id; ?>" class="act-btn btn-view" title="عرض الفاتورة"><i class="fas fa-eye"></i></a>
                                        <button class="act-btn btn-print" title="طباعة" onclick="window.print()"><i class="fas fa-print"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($data['invoices'])) : ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-file-invoice"></i>
                                        <h4>لا توجد فواتير بعد</h4>
                                        <p>ابدأ بإنشاء أول فاتورة من الزر أدناه</p>
                                        <a href="<?php echo URL_ROOT; ?>/sale/create" class="btn-add" style="display:inline-flex;"><i class="fas fa-plus"></i> إنشاء فاتورة</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.inv-row');
        const visibleCount = document.getElementById('visibleCount');

        searchInput.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            let c = 0;
            rows.forEach(r => {
                const t = (r.getAttribute('data-search') || '').toLowerCase();
                const m = t.includes(q);
                r.style.display = m ? '' : 'none';
                if (m) c++;
            });
            visibleCount.textContent = c;
        });

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>