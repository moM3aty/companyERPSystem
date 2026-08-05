<?php
// app/views/dashboard/index.php
// =====================================================
// الملف الكامل للوحة التحكم مع جميع التنسيقات والقوائم
// =====================================================
extract($data);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <!-- الخطوط والأيقونات -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        /* ==========================================
           المتغيرات الأساسية (نفس الملفات الأخرى)
           ========================================== */
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

        /* ==========================================
           الشريط الجانبي (Sidebar)
           ========================================== */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            border-left: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-brand {
            padding: 24px 24px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
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
            text-transform: uppercase; letter-spacing: 1.5px;
            padding: 12px 14px 8px;
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

        .nav-link.active {
            background: rgba(20,184,166,0.1);
            color: var(--primary); font-weight: 600;
        }

        .nav-link.active::before {
            content: ''; position: absolute; right: -12px; top: 50%;
            transform: translateY(-50%); width: 3px; height: 24px;
            background: var(--primary); border-radius: 0 4px 4px 0;
        }

        .nav-badge {
            margin-right: auto;
            background: var(--accent);
            color: #fff;
            font-size: 10px; font-weight: 700;
            padding: 2px 8px; border-radius: 10px;
            min-width: 22px; text-align: center;
        }

        .sidebar-user {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 12px;
        }

        .sidebar-user .su-avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0;
        }

        .sidebar-user .su-info { flex: 1; min-width: 0; }

        .sidebar-user .su-name {
            font-size: 13px; font-weight: 600; color: #e2e8f0;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }

        .sidebar-user .su-logout {
            color: var(--text-muted); font-size: 14px; padding: 6px;
            border-radius: 8px; transition: all 0.2s; text-decoration: none;
        }

        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239,68,68,0.1); }

        /* ==========================================
           المحتوى الرئيسي
           ========================================== */
        .main-content {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
        }

        /* ==========================================
           الشريط العلوي (Topbar)
           ========================================== */
        .topbar {
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title { font-size: 18px; font-weight: 700; color: var(--text-dark); }

        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: var(--text-muted); margin-top: 2px;
        }

        .breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .breadcrumb a:hover { color: var(--primary); }

        .topbar-left { display: flex; align-items: center; gap: 8px; }

        .topbar-btn {
            width: 40px; height: 40px; border-radius: 10px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-body);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; font-size: 15px;
        }

        .topbar-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }

        .mobile-menu-btn { display: none; }

        /* ==========================================
           جسم الصفحة (Page Body)
           ========================================== */
        .page-body {
            padding: 28px 32px 40px;
        }

        /* ==========================================
           رسائل التنبيه (Flash)
           ========================================== */
        .flash-msg {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px; font-weight: 600;
            margin-bottom: 24px;
            animation: slideDown 0.4s ease both;
            border: 1px solid transparent;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .flash-msg.flash-success { background: var(--success-light); color: #15803d; border-color: #bbf7d0; }
        .flash-msg.flash-error { background: var(--danger-light); color: #dc2626; border-color: #fecaca; }
        .flash-msg.flash-warning { background: var(--accent-light); color: #b45309; border-color: #fde68a; }
        .flash-msg i { font-size: 16px; }

        /* ==========================================
           بطاقات الإحصائيات
           ========================================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .stat-card .sc-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .stat-card .sc-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        .stat-card .sc-label {
            font-size: 12px; color: var(--text-muted); font-weight: 500;
        }

        .stat-card .sc-value {
            font-size: 24px; font-weight: 800; color: var(--text-dark);
            font-variant-numeric: tabular-nums;
        }

        .stat-card .sc-sub {
            font-size: 12px; margin-top: 4px; color: var(--text-muted);
        }

        .section-title {
            font-size: 16px; font-weight: 700; color: var(--text-dark);
            margin: 24px 0 14px 0;
            display: flex; align-items: center; gap: 8px;
        }

        .section-title i { color: var(--primary); }

        /* ==========================================
           الرسم البياني
           ========================================== */
        .chart-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            margin-bottom: 28px;
        }

        .chart-card h3 {
            font-size: 15px; font-weight: 700; color: var(--text-dark);
            margin-bottom: 16px;
        }

        .chart-wrap {
            position: relative;
            height: 260px;
        }

        /* ==========================================
           الأنشطة الأخيرة
           ========================================== */
        .activities-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 20px;
        }

        .activities-card h3 {
            font-size: 15px; font-weight: 700; color: var(--text-dark);
            margin-bottom: 16px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child { border-bottom: none; }

        .activity-item .act-icon {
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
        }

        .activity-item .act-icon.insert { background: var(--success-light); color: var(--success); }
        .activity-item .act-icon.update { background: var(--accent-light); color: var(--accent); }
        .activity-item .act-icon.delete { background: var(--danger-light); color: var(--danger); }
        .activity-item .act-icon.login { background: var(--info-light); color: var(--info); }

        .activity-item .act-text { flex: 1; font-size: 13px; }
        .activity-item .act-text strong { color: var(--text-dark); }
        .activity-item .act-time { font-size: 11px; color: var(--text-muted); }

        /* ==========================================
           استجابة الشاشات
           ========================================== */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }

        .sidebar-overlay.show { display: block; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>

    <!-- ==========================================
    طبقة التعتيم للقائمة في الموبايل
    ========================================== -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ==========================================
    الشريط الجانبي (Sidebar)
    ========================================== -->
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
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/dashboard" class="nav-link active">
                    <i class="fas fa-gauge-high"></i><span>لوحة التحكم</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/employee" class="nav-link">
                    <i class="fas fa-users"></i><span>الموظفين</span>
                    <span class="nav-badge"><?php echo $total_employees ?? 0; ?></span>
                </a>
            </div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/product" class="nav-link"><i class="fas fa-boxes-stacked"></i><span>المخزون</span></a>
            </div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/sale" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a>
            </div>

            <div class="nav-section-title" style="margin-top:12px;">الموارد البشرية</div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/leave" class="nav-link"><i class="fas fa-calendar-check"></i><span>الإجازات</span></a>
            </div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/attendance" class="nav-link"><i class="fas fa-clock"></i><span>الحضور</span></a>
            </div>

            <div class="nav-section-title" style="margin-top:12px;">المشتريات والمخزون</div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/purchase" class="nav-link"><i class="fas fa-cart-plus"></i><span>أوامر الشراء</span></a>
            </div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/warehouse" class="nav-link"><i class="fas fa-warehouse"></i><span>المستودعات</span></a>
            </div>

            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/account/ledger" class="nav-link"><i class="fas fa-book"></i><span>دفتر الأستاذ</span></a>
            </div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/account/balance-sheet" class="nav-link"><i class="fas fa-scale-balanced"></i><span>الميزانية</span></a>
            </div>

            <div class="nav-section-title" style="margin-top:12px;">CRM والمشاريع</div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/opportunity" class="nav-link"><i class="fas fa-bullseye"></i><span>الفرص</span></a>
            </div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/project" class="nav-link"><i class="fas fa-diagram-project"></i><span>المشاريع</span></a>
            </div>

            <div class="nav-section-title" style="margin-top:12px;">النظام</div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/settings" class="nav-link"><i class="fas fa-gear"></i><span>الإعدادات</span></a>
            </div>
            <div class="nav-item">
                <a href="<?php echo URL_ROOT; ?>/audit" class="nav-link"><i class="fas fa-clipboard-list"></i><span>سجل التدقيق</span></a>
            </div>
        </nav>

        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo $_SESSION['user_name'] ?? ''; ?></div>
                <div class="su-role"><?php echo $_SESSION['user_role'] ?? 'مدير النظام'; ?></div>
            </div>
            <a href="<?php echo URL_ROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- ==========================================
    المحتوى الرئيسي
    ========================================== -->
    <div class="main-content">

        <!-- ===== الشريط العلوي (Topbar) ===== -->
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="topbar-btn mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="page-title"><?php echo $data['title']; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>لوحة التحكم</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <button class="topbar-btn" title="البحث" aria-label="البحث"><i class="fas fa-search"></i></button>
                <button class="topbar-btn" title="الإشعارات" aria-label="الإشعارات"><i class="fas fa-bell"></i></button>
                <button class="topbar-btn" title="الإعدادات" aria-label="الإعدادات"><i class="fas fa-gear"></i></button>
            </div>
        </header>

        <!-- ===== جسم الصفحة ===== -->
        <div class="page-body">

            <!-- ===== رسائل التنبيه (Flash) ===== -->
            <?php if (!empty($flash)) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- ===== 1. الموارد البشرية ===== -->
            <div class="section-title"><i class="fas fa-users"></i> الموارد البشرية</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">إجمالي الموظفين</span>
                        <div class="sc-icon" style="background:var(--info-light);color:var(--info);"><i class="fas fa-user-tie"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($total_employees ?? 0); ?></div>
                    <div class="sc-sub">موظف مسجل</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">الحضور اليوم</span>
                        <div class="sc-icon" style="background:var(--success-light);color:var(--success);"><i class="fas fa-clipboard-check"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($present_today ?? 0); ?></div>
                    <div class="sc-sub">موظف حاضر اليوم</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">إجازات معلقة</span>
                        <div class="sc-icon" style="background:var(--accent-light);color:var(--accent);"><i class="fas fa-calendar-clock"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($pending_leaves ?? 0); ?></div>
                    <div class="sc-sub">في انتظار الموافقة</div>
                </div>
            </div>

            <!-- ===== 2. المالية ===== -->
            <div class="section-title"><i class="fas fa-coins"></i> المالية والمحاسبة</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">إجمالي الإيرادات</span>
                        <div class="sc-icon" style="background:var(--success-light);color:var(--success);"><i class="fas fa-arrow-trend-up"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($total_sales ?? 0, 0); ?></div>
                    <div class="sc-sub">ر.س</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">إجمالي المصروفات</span>
                        <div class="sc-icon" style="background:var(--danger-light);color:var(--danger);"><i class="fas fa-arrow-trend-down"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($total_expenses ?? 0, 0); ?></div>
                    <div class="sc-sub">ر.س</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">صافي الربح</span>
                        <div class="sc-icon" style="background:var(--primary-light);color:var(--primary-dark);"><i class="fas fa-wallet"></i></div>
                    </div>
                    <div class="sc-value" style="color:<?php echo ($net_profit ?? 0) >= 0 ? 'var(--success)' : 'var(--danger)'; ?>;">
                        <?php echo number_format($net_profit ?? 0, 0); ?>
                    </div>
                    <div class="sc-sub">ر.س</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">عدد الفواتير</span>
                        <div class="sc-icon" style="background:var(--purple-light);color:var(--purple);"><i class="fas fa-receipt"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($invoice_count ?? 0); ?></div>
                    <div class="sc-sub">فاتورة مسجلة</div>
                </div>
            </div>

            <!-- ===== 3. الرسم البياني ===== -->
            <div class="chart-card">
                <h3><i class="fas fa-chart-line" style="color:var(--primary);"></i> تطور المبيعات الشهرية</h3>
                <div class="chart-wrap">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- ===== 4. المشتريات والمخزون ===== -->
            <div class="section-title"><i class="fas fa-warehouse"></i> المشتريات والمخزون</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">أوامر شراء معلقة</span>
                        <div class="sc-icon" style="background:var(--accent-light);color:var(--accent);"><i class="fas fa-clock"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($pending_orders ?? 0); ?></div>
                    <div class="sc-sub">في انتظار المعالجة</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">قيمة المخزون الإجمالية</span>
                        <div class="sc-icon" style="background:var(--info-light);color:var(--info);"><i class="fas fa-boxes-stacked"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($total_stock_value ?? 0, 0); ?></div>
                    <div class="sc-sub">ر.س</div>
                </div>
                <?php if (!empty($warehouse_stock)) : ?>
                    <?php foreach ($warehouse_stock as $wh) : ?>
                    <div class="stat-card">
                        <div class="sc-top">
                            <span class="sc-label"><?php echo htmlspecialchars($wh->name); ?></span>
                            <div class="sc-icon" style="background:var(--primary-light);color:var(--primary-dark);"><i class="fas fa-warehouse"></i></div>
                        </div>
                        <div class="sc-value"><?php echo number_format($wh->value, 0); ?></div>
                        <div class="sc-sub">ر.س</div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ===== 5. CRM والمشاريع ===== -->
            <div class="section-title"><i class="fas fa-bullseye"></i> CRM والمشاريع</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">الفرص المفتوحة</span>
                        <div class="sc-icon" style="background:var(--info-light);color:var(--info);"><i class="fas fa-flag"></i></div>
                    </div>
                    <div class="sc-value"><?php echo $opportunity_stats->open ?? 0; ?></div>
                    <div class="sc-sub">قيمة تقديرية: <?php echo number_format($opportunity_stats->total_value ?? 0, 0); ?> ر.س</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">الفرص المغلقة (فوز)</span>
                        <div class="sc-icon" style="background:var(--success-light);color:var(--success);"><i class="fas fa-trophy"></i></div>
                    </div>
                    <div class="sc-value"><?php echo $opportunity_stats->won ?? 0; ?></div>
                    <div class="sc-sub"><?php echo $opportunity_stats->lost ?? 0; ?> خسارة</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">المشاريع النشطة</span>
                        <div class="sc-icon" style="background:var(--primary-light);color:var(--primary-dark);"><i class="fas fa-diagram-project"></i></div>
                    </div>
                    <div class="sc-value"><?php echo $project_stats->active ?? 0; ?></div>
                    <div class="sc-sub">إجمالي: <?php echo $project_stats->total ?? 0; ?> مشروع</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">ميزانية المشاريع</span>
                        <div class="sc-icon" style="background:var(--purple-light);color:var(--purple);"><i class="fas fa-money-bill"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($project_stats->total_budget ?? 0, 0); ?></div>
                    <div class="sc-sub">ر.س</div>
                </div>
            </div>

            <!-- ===== 6. الأصول الثابتة ===== -->
            <div class="section-title"><i class="fas fa-building"></i> الأصول الثابتة</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">إجمالي الأصول</span>
                        <div class="sc-icon" style="background:var(--info-light);color:var(--info);"><i class="fas fa-cubes"></i></div>
                    </div>
                    <div class="sc-value"><?php echo $asset_stats->total ?? 0; ?></div>
                    <div class="sc-sub">أصل مسجل</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">التكلفة الإجمالية</span>
                        <div class="sc-icon" style="background:var(--accent-light);color:var(--accent);"><i class="fas fa-coins"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($asset_stats->total_cost ?? 0, 0); ?></div>
                    <div class="sc-sub">ر.س</div>
                </div>
                <div class="stat-card">
                    <div class="sc-top">
                        <span class="sc-label">القيمة الحالية</span>
                        <div class="sc-icon" style="background:var(--success-light);color:var(--success);"><i class="fas fa-chart-simple"></i></div>
                    </div>
                    <div class="sc-value"><?php echo number_format($asset_stats->total_current_value ?? 0, 0); ?></div>
                    <div class="sc-sub">بعد الإهلاك</div>
                </div>
            </div>

            <!-- ===== 7. الأنشطة الأخيرة ===== -->
            <div class="activities-card">
                <h3><i class="fas fa-clock-rotate-left" style="color:var(--info);"></i> آخر الأنشطة في النظام</h3>
                <?php if (!empty($recent_activities)) : ?>
                    <?php foreach ($recent_activities as $act) : 
                        $iconClass = match($act->action) {
                            'insert' => 'insert', 'update' => 'update', 'delete' => 'delete', 'login' => 'login',
                            default => 'update'
                        };
                        $actionName = match($act->action) {
                            'insert' => 'أضاف', 'update' => 'عدّل', 'delete' => 'حذف', 'login' => 'سجّل دخول',
                            default => $act->action
                        };
                    ?>
                    <div class="activity-item">
                        <div class="act-icon <?php echo $iconClass; ?>">
                            <i class="fas <?php echo match($iconClass) {
                                'insert' => 'fa-plus', 'update' => 'fa-pen', 'delete' => 'fa-trash', 'login' => 'fa-right-to-bracket',
                                default => 'fa-circle'
                            }; ?>"></i>
                        </div>
                        <div class="act-text">
                            <strong><?php echo htmlspecialchars($act->user_name ?? 'مستخدم'); ?></strong>
                            <?php echo $actionName; ?>
                            في جدول <strong><?php echo $act->table_name; ?></strong>
                            <?php if ($act->record_id) : ?>
                                (رقم <?php echo $act->record_id; ?>)
                            <?php endif; ?>
                            <div class="act-time"><?php echo date('Y-m-d H:i', strtotime($act->created_at)); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="activity-item" style="justify-content:center;color:var(--text-muted);">لا توجد أنشطة مسجلة بعد</div>
                <?php endif; ?>
            </div>

        </div> <!-- .page-body -->
    </div> <!-- .main-content -->

    <!-- ==========================================
    سكريبتات الجافاسكريبت
    ========================================== -->
    <script>
        // ===== الرسم البياني للمبيعات الشهرية =====
        (function() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            const salesData = <?php echo json_encode($sales_chart ?? array_fill(0, 12, 0)); ?>;
            const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'المبيعات الشهرية (ر.س)',
                        data: salesData,
                        backgroundColor: 'rgba(20, 184, 166, 0.7)',
                        borderColor: '#14b8a6',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            rtl: true,
                            titleFont: { family: 'Cairo' },
                            bodyFont: { family: 'Cairo' },
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.parsed.y.toLocaleString('ar-SA') + ' ر.س';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Cairo', size: 11 }, color: '#94a3b8' }
                        },
                        y: {
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: {
                                font: { family: 'Cairo', size: 11 },
                                color: '#94a3b8',
                                callback: function(v) { return v >= 1000 ? (v/1000).toFixed(0) + 'K' : v; }
                            }
                        }
                    }
                }
            });
        })();

        // ===== قائمة الموبايل =====
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');

        if (menuBtn) {
            menuBtn.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }

        // إغلاق القائمة عند تغيير حجم الشاشة (إذا أصبحت كبيرة)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
        });
    </script>

</body>
</html>