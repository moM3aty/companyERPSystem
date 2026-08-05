<?php
// app/views/products/index.php
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
            --primary: #14b8a6;
            --primary-dark: #0d9488;
            --primary-light: #ccfbf1;
            --accent: #f59e0b;
            --accent-light: #fef3c7;
            --success: #22c55e;
            --success-light: #dcfce7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --info: #06b6d4;
            --info-light: #cffafe;
            --purple: #8b5cf6;
            --purple-light: #ede9fe;
            --sidebar-w: 272px;
            --topbar-h: 68px;
            --page-bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-body: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --radius: 14px;
            --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--page-bg);
            color: var(--text-body);
            min-height: 100vh;
        }

        /* === الشريط الجانبي === */
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

        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; }
        .sidebar-brand .s-name span { color: var(--primary); }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section-title {
            font-size: 10px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: var(--radius-sm);
            color: #94a3b8; text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: all 0.2s ease; position: relative;
        }

        .nav-link i { width: 20px; text-align: center; font-size: 15px; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }

        .nav-link.active {
            background: rgba(20,184,166,0.1);
            color: var(--primary); font-weight: 600;
        }

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

        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }

        .sidebar-user .su-logout {
            color: var(--text-muted); font-size: 14px; padding: 6px;
            border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto;
        }

        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239,68,68,0.1); }

        /* === المحتوى === */
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

        .topbar-left { display: flex; align-items: center; gap: 8px; }

        .topbar-btn {
            width: 40px; height: 40px; border-radius: 10px;
            border: 1px solid var(--border); background: transparent;
            color: var(--text-body); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; font-size: 15px;
        }

        .topbar-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }
        .mobile-menu-btn { display: none; }

        .page-body { padding: 28px 32px 40px; }

        /* === بطاقات ملخص المخزون === */
        .stock-summary {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 16px; margin-bottom: 24px;
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ss-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            padding: 20px; display: flex; align-items: center; gap: 14px;
            transition: all 0.25s;
        }

        .ss-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }

        .ss-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        .ss-card:nth-child(1) .ss-icon { background: var(--primary-light); color: var(--primary-dark); }
        .ss-card:nth-child(2) .ss-icon { background: var(--success-light); color: var(--success); }
        .ss-card:nth-child(3) .ss-icon { background: var(--accent-light); color: var(--accent); }
        .ss-card:nth-child(4) .ss-icon { background: var(--danger-light); color: var(--danger); }

        .ss-label { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-bottom: 2px; }
        .ss-value { font-size: 22px; font-weight: 800; color: var(--text-dark); font-variant-numeric: tabular-nums; }

        /* === شريط الأدوات === */
        .toolbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; margin-bottom: 20px; flex-wrap: wrap;
            animation: fadeUp 0.5s ease 0.1s both;
        }

        .toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        .search-box { position: relative; }

        .search-box input {
            width: 260px; padding: 10px 16px 10px 40px;
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

        .filter-select {
            padding: 10px 14px; padding-left: 32px;
            border: 1.5px solid var(--border); border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 13px;
            background: var(--card-bg); color: var(--text-body);
            outline: none; cursor: pointer; transition: border-color 0.2s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: left 12px center;
        }

        .filter-select:focus { border-color: var(--primary); }

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

        /* === جدول المنتجات === */
        .table-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            overflow: hidden; animation: fadeUp 0.5s ease 0.2s both;
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

        tbody td {
            padding: 14px 20px; font-size: 13.5px;
            color: var(--text-body); white-space: nowrap;
        }

        /* خلية المنتج */
        .prod-cell { display: flex; align-items: center; gap: 14px; }

        .prod-icon {
            width: 42px; height: 42px; border-radius: 10px;
            background: linear-gradient(135deg, var(--purple-light), #e0e7ff);
            display: flex; align-items: center; justify-content: center;
            color: var(--purple); font-size: 16px; flex-shrink: 0;
        }

        .prod-name { font-weight: 600; color: var(--text-dark); font-size: 13.5px; }
        .prod-sku { font-size: 11px; color: var(--text-muted); font-family: monospace; direction: ltr; display: inline-block; }

        /* شارة التصنيف */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 600;
        }

        .badge-cat { background: var(--purple-light); color: #6d28d9; }
        .badge-cat i { font-size: 10px; }

        /* مؤشر المخزون */
        .stock-indicator { display: flex; align-items: center; gap: 10px; min-width: 140px; }

        .stock-bar-wrap {
            flex: 1; height: 6px; background: #f1f5f9;
            border-radius: 3px; overflow: hidden;
        }

        .stock-bar {
            height: 100%; border-radius: 3px;
            transition: width 0.6s ease;
        }

        .stock-bar.sb-high { background: var(--success); }
        .stock-bar.sb-mid { background: var(--accent); }
        .stock-bar.sb-low { background: var(--danger); }

        .stock-num { font-size: 13px; font-weight: 700; color: var(--text-dark); min-width: 32px; text-align: left; font-variant-numeric: tabular-nums; }

        .stock-tag {
            font-size: 10px; font-weight: 700; padding: 2px 8px;
            border-radius: 6px; white-space: nowrap;
        }

        .stock-tag.st-ok { background: var(--success-light); color: #15803d; }
        .stock-tag.st-warn { background: var(--accent-light); color: #b45309; }
        .stock-tag.st-crit { background: var(--danger-light); color: #dc2626; animation: pulse 2s ease-in-out infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* السعر */
        .price-val { font-weight: 700; color: var(--text-dark); font-variant-numeric: tabular-nums; }
        .price-val .currency { font-size: 11px; font-weight: 500; color: var(--text-muted); margin-right: 2px; }

        /* أزرار الإجراءات */
        .actions-cell { display: flex; align-items: center; gap: 6px; }

        .act-btn {
            width: 34px; height: 34px; border-radius: 8px;
            border: 1px solid var(--border); background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 13px; transition: all 0.2s; text-decoration: none;
        }

        .act-btn.btn-edit { color: var(--accent); }
        .act-btn.btn-edit:hover { background: var(--accent-light); border-color: var(--accent); }
        .act-btn.btn-del { color: var(--danger); }
        .act-btn.btn-del:hover { background: var(--danger-light); border-color: var(--danger); }

        /* حالة فارغة */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 48px; color: var(--border); margin-bottom: 16px; }
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

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
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

        /* استجابة */
        @media (max-width: 1200px) { .stock-summary { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
            .search-box input { width: 200px; }
            .toolbar { flex-direction: column; align-items: flex-start; }
            .stock-summary { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 480px) { .stock-summary { grid-template-columns: 1fr; } }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.show { display: block; }

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
            <div class="s-name">ERP <span>Pro</span></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">القائمة الرئيسية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/dashboard" class="nav-link"><i class="fas fa-gauge-high"></i><span>لوحة التحكم</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/employee" class="nav-link"><i class="fas fa-users"></i><span>الموظفين</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/product" class="nav-link active"><i class="fas fa-boxes-stacked"></i><span>المخزون</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/sale" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
        </nav>
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div>
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
                    <div class="page-title"><?php echo $data['title']; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>المخزون</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <button class="topbar-btn" title="تصدير Excel" aria-label="تصدير"><i class="fas fa-file-excel"></i></button>
                <button class="topbar-btn" title="طباعة" aria-label="طباعة" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">

            <?php
                $totalProducts = count($data['products']);
                $lowStock = 0;
                $totalValue = 0;
                $outOfStock = 0;
                foreach($data['products'] as $p) {
                    $totalValue += ($p->quantity * $p->price);
                    if($p->quantity <= 0) $outOfStock++;
                    elseif($p->quantity <= 10) $lowStock++;
                }
            ?>

            <!-- ملخص المخزون -->
            <div class="stock-summary">
                <div class="ss-card">
                    <div class="ss-icon"><i class="fas fa-boxes-stacked"></i></div>
                    <div>
                        <div class="ss-label">إجمالي المنتجات</div>
                        <div class="ss-value"><?php echo $totalProducts; ?></div>
                    </div>
                </div>
                <div class="ss-card">
                    <div class="ss-icon"><i class="fas fa-coins"></i></div>
                    <div>
                        <div class="ss-label">قيمة المخزون</div>
                        <div class="ss-value"><?php echo number_format($totalValue, 0); ?> <span style="font-size:12px;color:var(--text-muted);font-weight:500;">ر.س</span></div>
                    </div>
                </div>
                <div class="ss-card">
                    <div class="ss-icon"><i class="fas fa-triangle-exclamation"></i></div>
                    <div>
                        <div class="ss-label">مخزون منخفض</div>
                        <div class="ss-value" style="color:var(--accent);"><?php echo $lowStock; ?></div>
                    </div>
                </div>
                <div class="ss-card">
                    <div class="ss-icon"><i class="fas fa-ban"></i></div>
                    <div>
                        <div class="ss-label">نفذ من المخزون</div>
                        <div class="ss-value" style="color:var(--danger);"><?php echo $outOfStock; ?></div>
                    </div>
                </div>
            </div>

            <!-- شريط الأدوات -->
            <div class="toolbar">
                <div class="toolbar-right">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="ابحث بالاسم أو SKU أو التصنيف..." autocomplete="off">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:14px;">
                    <span class="result-count">عرض <strong id="visibleCount"><?php echo $totalProducts; ?></strong> من <strong><?php echo $totalProducts; ?></strong> منتج</span>
                    <a href="<?php echo URL_ROOT; ?>/product/create" class="btn-add">
                        <i class="fas fa-plus"></i> إضافة منتج
                    </a>
                </div>
            </div>

            <!-- الجدول -->
            <div class="table-card">
                <div class="table-wrap">
                    <table id="prodTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المنتج</th>
                                <th>التصنيف</th>
                                <th>المخزون</th>
                                <th>السعر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['products'] as $prod) :
                                $maxQty = 100; // للحساب النسبي
                                $pct = min(($prod->quantity / $maxQty) * 100, 100);
                                $barClass = $prod->quantity <= 0 ? 'sb-low' : ($prod->quantity <= 10 ? 'sb-mid' : 'sb-high');
                                $tagClass = $prod->quantity <= 0 ? 'st-crit' : ($prod->quantity <= 10 ? 'st-warn' : 'st-ok');
                                $tagText = $prod->quantity <= 0 ? 'نفذ' : ($prod->quantity <= 10 ? 'منخفض' : 'متوفر');
                            ?>
                            <tr class="prod-row" data-search="<?php echo htmlspecialchars($prod->name . ' ' . $prod->sku . ' ' . ($prod->cat_name ?? '')); ?>">
                                <td style="color:var(--text-muted);font-weight:600;font-size:12px;"><?php echo $prod->id; ?></td>
                                <td>
                                    <div class="prod-cell">
                                        <div class="prod-icon"><i class="fas fa-box"></i></div>
                                        <div>
                                            <div class="prod-name"><?php echo htmlspecialchars($prod->name); ?></div>
                                            <div class="prod-sku"><?php echo htmlspecialchars($prod->sku); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if(!empty($prod->cat_name)) : ?>
                                        <span class="badge badge-cat"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($prod->cat_name); ?></span>
                                    <?php else : ?>
                                        <span style="color:var(--text-muted);font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="stock-indicator">
                                        <span class="stock-num"><?php echo $prod->quantity; ?></span>
                                        <div class="stock-bar-wrap">
                                            <div class="stock-bar <?php echo $barClass; ?>" style="width:<?php echo $pct; ?>%;"></div>
                                        </div>
                                        <span class="stock-tag <?php echo $tagClass; ?>"><?php echo $tagText; ?></span>
                                    </div>
                                </td>
                                <td><span class="price-val"><?php echo number_format($prod->price, 2); ?> <span class="currency">ر.س</span></span></td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="<?php echo URL_ROOT; ?>/product/edit/<?php echo $prod->id; ?>" class="act-btn btn-edit" title="تعديل"><i class="fas fa-pen-to-square"></i></a>
                                        <button class="act-btn btn-del" title="حذف" onclick="openDeleteModal(<?php echo $prod->id; ?>, '<?php echo htmlspecialchars(addslashes($prod->name)); ?>')"><i class="fas fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if(empty($data['products'])) : ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-box-open"></i>
                                        <h4>لا توجد منتجات مسجلة</h4>
                                        <p>ابدأ بإضافة أول منتج إلى المخزون</p>
                                        <a href="<?php echo URL_ROOT; ?>/product/create" class="btn-add"><i class="fas fa-plus"></i> إضافة منتج</a>
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
                <h3>تأكيد حذف المنتج</h3>
                <p>هل أنت متأكد من حذف المنتج <strong id="delProdName"></strong>؟<br>لا يمكن التراجع عن هذا الإجراء.</p>
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
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.prod-row');
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

        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const delProdName = document.getElementById('delProdName');

        function openDeleteModal(id, name) {
            delProdName.textContent = name;
            deleteForm.action = '<?php echo URL_ROOT; ?>/product/delete/' + id;
            deleteModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            deleteModal.classList.remove('show');
            document.body.style.overflow = '';
        }

        deleteModal.addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeDeleteModal(); });

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>