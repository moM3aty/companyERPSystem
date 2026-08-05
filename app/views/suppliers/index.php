<?php
// app/views/suppliers/index.php
 $pageTitle = $data['title'] ?? 'إدارة الموردين';
 $suppliers = $data['suppliers'] ?? [];
 $search = $data['search'] ?? '';
 $filter = $data['filter'] ?? 'all';
 $totalPayables = $data['total_payables'] ?? 0;
 $flash = $data['flash'] ?? null;
 $currentUrl = $_GET['url'] ?? 'supplier/index';
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

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--page-bg);
            color: var(--text-body);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed; top: 0; right: 0;
            width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%);
            z-index: 100; display: flex; flex-direction: column;
            transition: transform 0.3s ease;
            border-left: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-brand {
            padding: 24px 24px 20px;
            display: flex; align-items: center; gap: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-brand .s-logo {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff; flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(20,184,166,0.25);
        }

        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; letter-spacing: -0.3px; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-brand .s-ver { font-size: 10px; color: var(--text-muted); margin-top: -2px; }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title {
            font-size: 10px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px;
        }

        .nav-item { margin-bottom: 2px; }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: var(--radius-sm);
            color: #94a3b8; text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: all 0.2s ease; position: relative;
        }
        .nav-link i { width: 20px; text-align: center; font-size: 15px; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }
        .nav-link.active { background: rgba(20,184,166,0.1); color: var(--primary); font-weight: 600; }
        .nav-link.active::before {
            content: ''; position: absolute; right: -12px; top: 50%;
            transform: translateY(-50%); width: 3px; height: 24px;
            background: var(--primary); border-radius: 0 4px 4px 0;
        }

        .sidebar-user {
            padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-user .su-avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0;
        }
        .sidebar-user .su-info { flex: 1; min-width: 0; }
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; }
        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239,68,68,0.1); }

        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            height: var(--topbar-h); background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px; position: sticky; top: 0; z-index: 50;
        }
        .page-title { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: var(--text-muted); margin-top: 2px;
        }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary); }

        .mobile-menu-btn { display: none; }
        .topbar-btn {
            width: 40px; height: 40px; border-radius: 10px;
            border: 1px solid var(--border); background: transparent;
            color: var(--text-body); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; font-size: 15px;
        }
        .topbar-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }

        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

        .flash-msg {
            padding: 14px 20px; border-radius: var(--radius-sm);
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; font-weight: 600;
            margin-bottom: 24px;
            animation: slideDown 0.4s ease both;
            border: 1px solid transparent;
        }
        .flash-msg.flash-success { background: var(--success-light); color: #15803d; border-color: #bbf7d0; }
        .flash-msg.flash-error { background: var(--danger-light); color: #dc2626; border-color: #fecaca; }
        .flash-msg.flash-warning { background: var(--accent-light); color: #b45309; border-color: #fde68a; }
        .flash-msg i { font-size: 16px; }

        /* بطاقات الملخص */
        .summary-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 16px; margin-bottom: 24px;
            animation: fadeUp 0.5s ease both;
        }

        .summary-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            padding: 22px; display: flex; align-items: center; gap: 16px;
            transition: all 0.3s;
        }
        .summary-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }

        .sc-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 19px; flex-shrink: 0;
        }
        .summary-card:nth-child(1) .sc-icon { background: var(--info-light); color: var(--info); }
        .summary-card:nth-child(2) .sc-icon { background: var(--danger-light); color: var(--danger); }
        .summary-card:nth-child(3) .sc-icon { background: var(--success-light); color: var(--success); }

        .sc-label { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-bottom: 2px; }
        .sc-value { font-size: 24px; font-weight: 800; color: var(--text-dark); font-variant-numeric: tabular-nums; }
        .sc-unit { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-right: 2px; }

        /* شريط الأدوات */
        .toolbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; margin-bottom: 20px; flex-wrap: wrap;
            animation: fadeUp 0.5s ease 0.1s both;
        }

        .toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .search-box { position: relative; }
        .search-box input {
            width: 280px; padding: 10px 16px 10px 40px;
            border: 1.5px solid var(--border); border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 13px;
            background: var(--card-bg); color: var(--text-dark);
            outline: none; transition: all 0.2s;
        }
        .search-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,184,166,0.08); }
        .search-box input::placeholder { color: var(--text-muted); }
        .search-box i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); color: var(--text-muted); font-size: 13px;
        }

        .filter-chip {
            padding: 8px 16px; border-radius: 20px;
            border: 1.5px solid var(--border); background: var(--card-bg);
            font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 600;
            color: var(--text-body); cursor: pointer; transition: all 0.2s;
        }
        .filter-chip:hover { border-color: var(--primary); color: var(--primary); }
        .filter-chip.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        .btn-add {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: all 0.25s; box-shadow: 0 2px 10px rgba(20,184,166,0.2);
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(20,184,166,0.3); }

        .result-count { font-size: 13px; color: var(--text-muted); }
        .result-count strong { color: var(--text-dark); font-weight: 700; }

        /* الجدول */
        .table-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            overflow: hidden; animation: fadeUp 0.5s ease 0.15s both;
        }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 14px 20px; font-size: 11px; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: 0.8px; background: #f8fafc;
            border-bottom: 1.5px solid var(--border);
            text-align: right; white-space: nowrap;
        }

        tbody tr { transition: background 0.15s; border-bottom: 1px solid var(--border); }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(20,184,166,0.02); }
        tbody td { padding: 14px 20px; font-size: 13.5px; color: var(--text-body); }

        .supplier-cell { display: flex; align-items: center; gap: 14px; }
        .supplier-avatar {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; color: #fff; flex-shrink: 0;
        }
        .supplier-avatar.av-company { background: linear-gradient(135deg, var(--purple), #7c3aed); }
        .supplier-avatar.av-individual { background: linear-gradient(135deg, var(--accent), #d97706); }

        .supplier-name { font-weight: 600; color: var(--text-dark); font-size: 13.5px; }
        .supplier-email { font-size: 12px; color: var(--text-muted); }

        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 600;
        }
        .badge-company { background: var(--purple-light); color: #6d28d9; }
        .badge-individual { background: var(--accent-light); color: #b45309; }
        .badge i { font-size: 10px; }

        .balance-val {
            font-weight: 700; font-variant-numeric: tabular-nums;
            direction: ltr; display: inline-block;
        }
        .balance-val.positive { color: var(--danger); }
        .balance-val.zero { color: var(--text-muted); }

        .actions-cell { display: flex; align-items: center; gap: 6px; }
        .act-btn {
            width: 34px; height: 34px; border-radius: 8px;
            border: 1px solid var(--border); background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 13px; transition: all 0.2s;
            text-decoration: none; color: var(--text-body);
        }
        .act-btn.btn-view { color: var(--primary); }
        .act-btn.btn-view:hover { background: var(--primary-light); border-color: var(--primary); }
        .act-btn.btn-edit { color: var(--accent); }
        .act-btn.btn-edit:hover { background: var(--accent-light); border-color: var(--accent); }
        .act-btn.btn-del { color: var(--danger); }
        .act-btn.btn-del:hover { background: var(--danger-light); border-color: var(--danger); }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 48px; color: var(--border); margin-bottom: 16px; display: block; }
        .empty-state h4 { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .empty-state p { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

        /* مودال الحذف */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,0.5); backdrop-filter: blur(4px);
            z-index: 200; align-items: center; justify-content: center;
        }
        .modal-overlay.show { display: flex; }

        .modal-box {
            background: var(--card-bg); border-radius: var(--radius);
            width: 420px; max-width: 90vw;
            box-shadow: 0 24px 60px rgba(0,0,0,0.2);
            animation: modalIn 0.3s ease; overflow: hidden;
        }
        .modal-header { padding: 24px 24px 0; text-align: center; }
        .modal-header .modal-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: var(--danger-light); color: var(--danger);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 24px; margin-bottom: 16px;
        }
        .modal-header h3 { font-size: 17px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .modal-header p { font-size: 13px; color: var(--text-muted); line-height: 1.7; }
        .modal-header p strong { color: var(--text-dark); }

        .modal-footer { padding: 20px 24px 24px; display: flex; gap: 10px; justify-content: center; }
        .modal-btn {
            padding: 10px 28px; border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600;
            cursor: pointer; border: none; transition: all 0.2s;
        }
        .modal-btn.btn-cancel { background: var(--page-bg); color: var(--text-body); border: 1px solid var(--border); }
        .modal-btn.btn-cancel:hover { background: var(--border); }
        .modal-btn.btn-confirm-del { background: var(--danger); color: #fff; box-shadow: 0 2px 8px rgba(239,68,68,0.25); }
        .modal-btn.btn-confirm-del:hover { background: #dc2626; box-shadow: 0 4px 14px rgba(239,68,68,0.35); }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
            .search-box input { width: 200px; }
            .toolbar { flex-direction: column; align-items: flex-start; }
            .summary-grid { grid-template-columns: 1fr; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text">
                <span class="s-name">ERP <span>Pro</span></span>
                <span class="s-ver">v<?php echo APP_VERSION; ?></span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">القائمة الرئيسية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/dashboard" class="nav-link"><i class="fas fa-gauge-high"></i><span>لوحة التحكم</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/employee" class="nav-link"><i class="fas fa-users"></i><span>الموظفين</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/product" class="nav-link"><i class="fas fa-boxes-stacked"></i><span>المخزون</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/sale" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a></div>

            <div class="nav-section-title" style="margin-top:12px;">العلاقات</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/customer" class="nav-link"><i class="fas fa-address-book"></i><span>العملاء</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/supplier" class="nav-link active"><i class="fas fa-truck-field"></i><span>الموردين</span></a></div>

            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/report" class="nav-link"><i class="fas fa-chart-line"></i><span>التقارير</span></a></div>

            <div class="nav-section-title" style="margin-top:12px;">النظام</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/settings" class="nav-link"><i class="fas fa-gear"></i><span>الإعدادات</span></a></div>
        </nav>

        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo $_SESSION['user_name'] ?? ''; ?></div>
                <div class="su-role"><?php echo $_SESSION['user_role'] ?? 'مدير النظام'; ?></div>
            </div>
            <a href="<?php echo URL_ROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="topbar-btn mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $pageTitle; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>الموردين</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <button class="topbar-btn" title="تصدير" aria-label="تصدير"><i class="fas fa-file-export"></i></button>
                <button class="topbar-btn" title="طباعة" aria-label="طباعة" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">

            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : ($flash['type'] === 'error' ? 'circle-xmark' : 'triangle-exclamation'); ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- ملخص الموردين -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-truck"></i></div>
                    <div>
                        <div class="sc-label">إجمالي الموردين</div>
                        <div class="sc-value"><?php echo number_format($data['total_count'] ?? 0); ?></div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-hand-holding-dollar"></i></div>
                    <div>
                        <div class="sc-label">إجمالي المستحقات</div>
                        <div class="sc-value" style="color:var(--danger);">
                            <?php echo number_format($totalPayables, 2); ?> <span class="sc-unit">ر.س</span>
                        </div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="sc-label">موردين نشطين</div>
                        <div class="sc-value" style="color:var(--success);">
                            <?php echo count(array_filter($suppliers, function($s) { return $s->balance == 0; })); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- شريط الأدوات -->
            <div class="toolbar">
                <div class="toolbar-right">
                    <div class="search-box">
                        <input type="text" id="searchInput" 
                               placeholder="ابحث بالاسم أو الهاتف..." 
                               value="<?php echo htmlspecialchars($search); ?>"
                               autocomplete="off">
                        <i class="fas fa-search"></i>
                    </div>
                    <button class="filter-chip <?php echo $filter === 'all' ? 'active' : ''; ?>" data-filter="all">الكل</button>
                    <button class="filter-chip <?php echo $filter === 'individual' ? 'active' : ''; ?>" data-filter="individual">
                        <i class="fas fa-user" style="margin-left:4px;font-size:11px;"></i> أفراد
                    </button>
                    <button class="filter-chip <?php echo $filter === 'company' ? 'active' : ''; ?>" data-filter="company">
                        <i class="fas fa-building" style="margin-left:4px;font-size:11px;"></i> شركات
                    </button>
                </div>
                <div style="display:flex;align-items:center;gap:14px;">
                    <span class="result-count">
                        عرض <strong id="visibleCount"><?php echo count($suppliers); ?></strong> مورد
                    </span>
                    <a href="<?php echo URL_ROOT; ?>/supplier/create" class="btn-add">
                        <i class="fas fa-plus"></i> إضافة مورد
                    </a>
                </div>
            </div>

            <!-- الجدول -->
            <div class="table-card">
                <div class="table-wrap">
                    <table id="supplierTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المورد</th>
                                <th>النوع</th>
                                <th>جهة اتصال</th>
                                <th>الهاتف</th>
                                <th>المستحق</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="supplierBody">
                            <?php foreach ($suppliers as $s) :
                                $avClass = $s->type === 'company' ? 'av-company' : 'av-individual';
                            ?>
                            <tr class="supplier-row" data-type="<?php echo $s->type; ?>" data-search="<?php echo htmlspecialchars($s->name . ' ' . ($s->phone ?? '') . ' ' . ($s->contact_person ?? '')); ?>">
                                <td style="color:var(--text-muted);font-weight:600;font-size:12px;"><?php echo $s->id; ?></td>
                                <td>
                                    <div class="supplier-cell">
                                        <div class="supplier-avatar <?php echo $avClass; ?>">
                                            <?php echo Helpers::getInitials($s->name); ?>
                                        </div>
                                        <div>
                                            <div class="supplier-name">
                                                <a href="<?php echo URL_ROOT; ?>/supplier/view/<?php echo $s->id; ?>" style="color:inherit;text-decoration:none;">
                                                    <?php echo htmlspecialchars($s->name); ?>
                                                </a>
                                            </div>
                                            <div class="supplier-email"><?php echo htmlspecialchars($s->email ?? '—'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $s->type; ?>">
                                        <i class="fas fa-<?php echo $s->type === 'company' ? 'building' : 'user'; ?>"></i> 
                                        <?php echo $s->type === 'company' ? 'شركة' : 'فرد'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($s->contact_person ?? '—'); ?></td>
                                <td style="direction:ltr;text-align:right;"><?php echo htmlspecialchars($s->phone ?? '—'); ?></td>
                                <td>
                                    <?php if ($s->balance > 0) : ?>
                                        <span class="balance-val positive"><?php echo number_format($s->balance, 2); ?> ر.س</span>
                                    <?php else : ?>
                                        <span class="balance-val zero">0.00 ر.س</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="<?php echo URL_ROOT; ?>/supplier/view/<?php echo $s->id; ?>" class="act-btn btn-view" title="عرض"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo URL_ROOT; ?>/supplier/edit/<?php echo $s->id; ?>" class="act-btn btn-edit" title="تعديل"><i class="fas fa-pen-to-square"></i></a>
                                        <button class="act-btn btn-del" title="حذف" 
                                                onclick="openDeleteModal(<?php echo $s->id; ?>, '<?php echo htmlspecialchars(addslashes($s->name)); ?>', <?php echo $s->balance; ?>)">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($suppliers)) : ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-truck"></i>
                                        <h4>لا يوجد موردين مسجلين</h4>
                                        <p>ابدأ بإضافة أول مورد إلى النظام</p>
                                        <a href="<?php echo URL_ROOT; ?>/supplier/create" class="btn-add"><i class="fas fa-plus"></i> إضافة مورد</a>
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

    <!-- مودال الحذف -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon"><i class="fas fa-triangle-exclamation"></i></div>
                <h3>تأكيد حذف المورد</h3>
                <p>هل أنت متأكد من حذف المورد "<strong id="delSupplierName"></strong>"؟</p>
                <p id="balanceWarning" style="color:var(--danger);font-weight:600;font-size:12px;margin-top:8px;display:none;">
                    <i class="fas fa-exclamation-triangle"></i> تحذير: المورد لديه مستحقات
                </p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn btn-cancel" onclick="closeDeleteModal()">إلغاء</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    <button type="submit" class="modal-btn btn-confirm-del">نعم، احذف</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        /* البحث في الجدول */
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.supplier-row');
        const visibleCount = document.getElementById('visibleCount');

        searchInput.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            let count = 0;
            
            rows.forEach(row => {
                const text = (row.getAttribute('data-search') || '').toLowerCase();
                const match = text.includes(q);
                row.style.display = match ? '' : 'none';
                if (match) count++;
            });
            
            visibleCount.textContent = count;
        });

        /* فلترة */
        document.querySelectorAll('.filter-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                
                let count = 0;
                rows.forEach(row => {
                    const show = this.dataset.filter === 'all' || row.dataset.type === this.dataset.filter;
                    row.style.display = show ? '' : 'none';
                    if (show) count++;
                });
                visibleCount.textContent = count;
            });
        });

        /* مودال الحذف */
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const delSupplierName = document.getElementById('delSupplierName');
        const balanceWarning = document.getElementById('balanceWarning');

        function openDeleteModal(id, name, balance) {
            delSupplierName.textContent = name;
            
            if (balance > 0) {
                balanceWarning.style.display = 'block';
                balanceWarning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> تحذير: المورد لديه مستحقات بقيمة ' + 
                    balance.toLocaleString('ar-SA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
            } else {
                balanceWarning.style.display = 'none';
            }
            
            deleteForm.action = '<?php echo URL_ROOT; ?>/supplier/delete/' + id;
            deleteModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            deleteModal.classList.remove('show');
            document.body.style.overflow = '';
        }

        deleteModal.addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
        });

        /* قائمة الموبايل */
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');

        if (menuBtn) {
            menuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
        });
    </script>
</body>
</html>