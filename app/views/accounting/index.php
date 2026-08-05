<?php
// app/views/accounting/index.php
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* === بطاقات الملخص المالي === */
        .finance-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 20px; margin-bottom: 28px;
        }

        .finance-card {
            border-radius: var(--radius); padding: 24px;
            position: relative; overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
            animation: fadeUp 0.5s ease both;
        }

        .finance-card:nth-child(1) { animation-delay: 0.05s; }
        .finance-card:nth-child(2) { animation-delay: 0.15s; }
        .finance-card:nth-child(3) { animation-delay: 0.25s; }

        .finance-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        .fc-income {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #fff;
        }

        .fc-expense {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: #fff;
        }

        .fc-profit {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: #fff;
        }

        .finance-card::before {
            content: ''; position: absolute;
            width: 180px; height: 180px;
            background: rgba(255,255,255,0.08); border-radius: 50%;
            top: -60px; left: -40px;
        }

        .finance-card::after {
            content: ''; position: absolute;
            width: 120px; height: 120px;
            background: rgba(255,255,255,0.05); border-radius: 50%;
            bottom: -40px; right: 30px;
        }

        .fc-top {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 16px; position: relative; z-index: 2;
        }

        .fc-label { font-size: 13px; opacity: 0.9; font-weight: 500; }

        .fc-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        .fc-value {
            font-size: 30px; font-weight: 800;
            position: relative; z-index: 2;
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }

        .fc-unit { font-size: 14px; font-weight: 500; opacity: 0.8; margin-right: 4px; }

        .fc-bar {
            position: relative; z-index: 2;
            margin-top: 16px; height: 4px;
            background: rgba(255,255,255,0.2);
            border-radius: 2px; overflow: hidden;
        }

        .fc-bar-fill {
            height: 100%; border-radius: 2px;
            background: rgba(255,255,255,0.6);
            transition: width 1s ease;
        }

        /* === المحتوى: شارت + نموذج === */
        .content-grid {
            display: grid; grid-template-columns: 1.5fr 1fr;
            gap: 20px; margin-bottom: 28px;
        }

        .card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            overflow: hidden;
            animation: fadeUp 0.5s ease 0.3s both;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-header h3 {
            font-size: 15px; font-weight: 700; color: var(--text-dark);
            display: flex; align-items: center; gap: 8px;
        }

        .card-body { padding: 24px; }

        .chart-wrap { position: relative; height: 260px; }

        /* === نموذج المصروف === */
        .expense-form { display: flex; flex-direction: column; gap: 18px; }

        .ef-group { display: flex; flex-direction: column; }

        .ef-label {
            font-size: 13px; font-weight: 600; color: var(--text-body);
            margin-bottom: 8px;
        }

        .ef-input {
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 14px;
            background: var(--card-bg); color: var(--text-dark);
            outline: none; transition: all 0.25s;
        }

        .ef-input::placeholder { color: var(--text-muted); }

        .ef-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(20,184,166,0.08);
        }

        .ef-input.has-error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(239,68,68,0.08);
        }

        .ef-error {
            font-size: 11px; color: var(--danger); margin-top: 6px;
            display: flex; align-items: center; gap: 4px;
        }

        .ef-hint {
            font-size: 11px; color: var(--text-muted); margin-top: 6px;
        }

        select.ef-input {
            appearance: none; cursor: pointer; padding-left: 36px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: left 14px center;
        }

        .btn-expense {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 13px 24px;
            background: linear-gradient(135deg, var(--danger), #dc2626);
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: all 0.25s; margin-top: 4px;
            box-shadow: 0 2px 10px rgba(239,68,68,0.2);
        }

        .btn-expense:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239,68,68,0.3); }
        .btn-expense:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        /* === جدول المصروفات === */
        .table-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            overflow: hidden;
            animation: fadeUp 0.5s ease 0.4s both;
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
        tbody tr:hover { background: rgba(239,68,68,0.02); }

        tbody td { padding: 14px 20px; font-size: 13.5px; color: var(--text-body); }

        .exp-desc { font-weight: 600; color: var(--text-dark); }

        .exp-amount {
            font-weight: 700; color: var(--danger); font-variant-numeric: tabular-nums;
            direction: ltr; display: inline-block;
        }

        .exp-amount .curr { font-size: 11px; font-weight: 500; color: var(--text-muted); margin-right: 2px; }

        .exp-date {
            font-size: 12px; color: var(--text-muted);
            display: flex; align-items: center; gap: 6px;
        }

        .exp-date i { font-size: 11px; }

        .act-btn {
            width: 32px; height: 32px; border-radius: 8px;
            border: 1px solid var(--border); background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 12px; transition: all 0.2s;
            color: var(--danger);
        }

        .act-btn:hover { background: var(--danger-light); border-color: var(--danger); }

        .empty-state { text-align: center; padding: 48px 20px; }
        .empty-state i { font-size: 40px; color: var(--border); margin-bottom: 12px; }
        .empty-state h4 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px; }
        .empty-state p { font-size: 13px; color: var(--text-muted); }

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
        .modal-btn.btn-confirm-del:hover { background: #dc2626; }

        /* استجابة */
        @media (max-width: 1200px) { .content-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
            .finance-grid { grid-template-columns: 1fr; }
        }

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
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/product" class="nav-link"><i class="fas fa-boxes-stacked"></i><span>المخزون</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/sale" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link active"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
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
                        <span>المحاسبة والأرباح</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <button class="topbar-btn" title="تصدير تقرير" aria-label="تصدير"><i class="fas fa-file-pdf"></i></button>
                <button class="topbar-btn" title="طباعة" aria-label="طباعة" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">

            <?php
                $totalSales = $data['total_sales'];
                $totalExpenses = $data['total_expenses'];
                $netProfit = $data['net_profit'];
                $incomePct = $totalSales > 0 ? 100 : 0;
                $expensePct = $totalSales > 0 ? min(($totalExpenses / $totalSales) * 100, 100) : 0;
                $profitPct = $totalSales > 0 ? max(($netProfit / $totalSales) * 100, 0) : 0;
            ?>

            <!-- بطاقات الملخص المالي -->
            <div class="finance-grid">
                <div class="finance-card fc-income">
                    <div class="fc-top">
                        <span class="fc-label">إجمالي الإيرادات</span>
                        <div class="fc-icon"><i class="fas fa-arrow-trend-up"></i></div>
                    </div>
                    <div class="fc-value"><?php echo number_format($totalSales, 0); ?> <span class="fc-unit">ر.س</span></div>
                    <div class="fc-bar"><div class="fc-bar-fill" style="width:<?php echo $incomePct; ?>%;"></div></div>
                </div>
                <div class="finance-card fc-expense">
                    <div class="fc-top">
                        <span class="fc-label">إجمالي المصروفات</span>
                        <div class="fc-icon"><i class="fas fa-arrow-trend-down"></i></div>
                    </div>
                    <div class="fc-value"><?php echo number_format($totalExpenses, 0); ?> <span class="fc-unit">ر.س</span></div>
                    <div class="fc-bar"><div class="fc-bar-fill" style="width:<?php echo $expensePct; ?>%;"></div></div>
                </div>
                <div class="finance-card fc-profit">
                    <div class="fc-top">
                        <span class="fc-label">صافي الربح</span>
                        <div class="fc-icon"><i class="fas fa-wallet"></i></div>
                    </div>
                    <div class="fc-value"><?php echo number_format($netProfit, 0); ?> <span class="fc-unit">ر.س</span></div>
                    <div class="fc-bar"><div class="fc-bar-fill" style="width:<?php echo $profitPct; ?>%;"></div></div>
                </div>
            </div>

            <!-- شارت + نموذج المصروف -->
            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-bar" style="color:var(--primary);"></i> توزيع الإيرادات والمصروفات</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrap">
                            <canvas id="financeChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-receipt" style="color:var(--danger);"></i> تسجيل مصروف جديد</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo URL_ROOT; ?>/accounting/index" method="POST" id="expenseForm" class="expense-form" novalidate>
                            <div class="ef-group">
                                <label class="ef-label">تصنيف المصروف</label>
                                <select name="category" class="ef-input">
                                    <option value="تشغيلية">تشغيلية</option>
                                    <option value="رواتب">رواتب وأجور</option>
                                    <option value="إيجار">إيجار</option>
                                    <option value="كهرباء وماء">كهرباء وماء</option>
                                    <option value="صيانة">صيانة</option>
                                    <option value="تسويق">تسويق وإعلان</option>
                                    <option value="نقل">نقل وشحن</option>
                                    <option value="أخرى">أخرى</option>
                                </select>
                            </div>
                            <div class="ef-group">
                                <label class="ef-label">بيان المصروف <span style="color:var(--danger);">*</span></label>
                                <input type="text" name="description" class="ef-input" id="expDesc" placeholder="مثال: فاتورة كهرباء شهر يناير" required>
                                <div class="ef-hint">وصف تفصيلي للمصروف</div>
                            </div>
                            <div class="ef-group">
                                <label class="ef-label">المبلغ (ر.س) <span style="color:var(--danger);">*</span></label>
                                <input type="number" step="0.01" name="amount" class="ef-input" id="expAmount" placeholder="0.00" required style="direction:ltr;text-align:right;">
                            </div>
                            <button type="submit" class="btn-expense" id="btnExpSubmit">
                                <i class="fas fa-plus-circle"></i>
                                تسجيل المصروف
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- جدول المصروفات -->
            <div class="table-card">
                <div class="card-header">
                    <h3><i class="fas fa-list-ul" style="color:var(--accent);"></i> سجل المصروفات</h3>
                    <span style="font-size:12px;color:var(--text-muted);">
                        <?php echo count($data['expenses']); ?> مصروف مسجل
                    </span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>البيان</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['expenses'] as $i => $exp) : ?>
                            <tr>
                                <td style="color:var(--text-muted);font-weight:600;font-size:12px;"><?php echo $i + 1; ?></td>
                                <td class="exp-desc"><?php echo htmlspecialchars($exp->description); ?></td>
                                <td><span class="exp-amount"><span class="curr">ر.س</span> <?php echo number_format($exp->amount, 2); ?></span></td>
                                <td>
                                    <span class="exp-date">
                                        <i class="far fa-calendar"></i>
                                        <?php echo date('Y-m-d', strtotime($exp->created_at)); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="act-btn" title="حذف" onclick="openDeleteModal(<?php echo $exp->id; ?>, '<?php echo htmlspecialchars(addslashes($exp->description)); ?>', <?php echo $exp->amount; ?>)">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if(empty($data['expenses'])) : ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-receipt"></i>
                                        <h4>لا توجد مصروفات مسجلة</h4>
                                        <p>ابدأ بتسجيل أول مصروف من النموذج أعلاه</p>
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
                <h3>تأكيد حذف المصروف</h3>
                <p>هل أنت متأكد من حذف المصروف "<strong id="delExpName"></strong>" بقيمة <strong id="delExpAmount"></strong>؟</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn btn-cancel" onclick="closeDeleteModal()">إلغاء</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    <input type="hidden" name="delete_expense" value="1">
                    <button type="submit" class="modal-btn btn-confirm-del">نعم، احذف</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        /* === الشارت المالي === */
        (function renderChart() {
            const ctx = document.getElementById('financeChart');
            if (!ctx) return;

            const sales = <?php echo $totalSales ?? 0; ?>;
            const expenses = <?php echo $totalExpenses ?? 0; ?>;
            const profit = sales - expenses;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['الإيرادات', 'المصروفات', 'صافي الربح'],
                    datasets: [{
                        data: [Math.max(sales, 0), Math.max(expenses, 0), Math.max(profit, 0)],
                        backgroundColor: ['#22c55e', '#ef4444', '#14b8a6'],
                        borderWidth: 0,
                        hoverOffset: 12,
                        borderRadius: 6,
                        spacing: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true,
                            labels: {
                                font: { family: 'Cairo', size: 13, weight: '600' },
                                color: '#64748b',
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                padding: 24,
                            }
                        },
                        tooltip: {
                            rtl: true,
                            titleFont: { family: 'Cairo', size: 13 },
                            bodyFont: { family: 'Cairo', size: 12 },
                            padding: 14,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(ctx) {
                                    const val = ctx.parsed;
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ctx.label + ': ' + val.toLocaleString('ar-SA') + ' ر.س (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        })();

        /* === مودال الحذف === */
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const delExpName = document.getElementById('delExpName');
        const delExpAmount = document.getElementById('delExpAmount');

        function openDeleteModal(id, name, amount) {
            delExpName.textContent = name;
            delExpAmount.textContent = amount.toLocaleString('ar-SA', { minimumFractionDigits: 2 }) + ' ر.س';
            deleteForm.action = '<?php echo URL_ROOT; ?>/accounting/index?delete=' + id;
            deleteModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            deleteModal.classList.remove('show');
            document.body.style.overflow = '';
        }

        deleteModal.addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeDeleteModal(); });

        /* === تحقق نموذج المصروف === */
        const expenseForm = document.getElementById('expenseForm');
        const btnExpSubmit = document.getElementById('btnExpSubmit');

        expenseForm.addEventListener('submit', function(e) {
            let valid = true;
            const desc = document.getElementById('expDesc');
            const amount = document.getElementById('expAmount');

            expenseForm.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));

            if (!desc.value.trim()) {
                desc.classList.add('has-error');
                valid = false;
            }

            if (!amount.value || parseFloat(amount.value) <= 0) {
                amount.classList.add('has-error');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            } else {
                btnExpSubmit.disabled = true;
                btnExpSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التسجيل...';
            }
        });

        /* === قائمة الموبايل === */
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>