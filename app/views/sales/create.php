<?php
// app/views/sales/create.php
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

        .mobile-menu-btn { display: none; }

        .topbar-btn {
            width: 40px; height: 40px; border-radius: 10px;
            border: 1px solid var(--border); background: transparent;
            color: var(--text-body); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; font-size: 15px;
        }

        .topbar-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }

        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* === رأس الفاتورة === */
        .invoice-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: var(--radius); padding: 28px 32px;
            color: #fff; margin-bottom: 24px;
            position: relative; overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }

        .invoice-header::before {
            content: ''; position: absolute;
            width: 300px; height: 300px;
            background: rgba(20,184,166,0.08); border-radius: 50%;
            top: -120px; left: -80px;
        }

        .invoice-header::after {
            content: ''; position: absolute;
            width: 180px; height: 180px;
            background: rgba(245,158,11,0.06); border-radius: 50%;
            bottom: -80px; right: 50px;
        }

        .invoice-header .ih-top {
            display: flex; align-items: center; justify-content: space-between;
            position: relative; z-index: 2; margin-bottom: 20px;
        }

        .invoice-header .ih-title {
            display: flex; align-items: center; gap: 12px;
        }

        .invoice-header .ih-title .ih-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: rgba(20,184,166,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: var(--primary-light);
        }

        .invoice-header .ih-title h2 { font-size: 20px; font-weight: 700; }
        .invoice-header .ih-title p { font-size: 12px; color: #94a3b8; margin-top: 2px; }

        .invoice-number {
            font-family: monospace; direction: ltr;
            font-size: 14px; color: var(--accent);
            background: rgba(245,158,11,0.1);
            padding: 8px 16px; border-radius: 8px;
            position: relative; z-index: 2;
        }

        .invoice-header .ih-customer {
            position: relative; z-index: 2;
            max-width: 500px;
        }

        .invoice-header .ih-customer label {
            font-size: 11px; color: #94a3b8; text-transform: uppercase;
            letter-spacing: 1px; margin-bottom: 8px; display: block;
        }

        .invoice-header .ih-customer input {
            width: 100%; max-width: 400px;
            padding: 12px 16px; border-radius: var(--radius-sm);
            border: 1.5px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.06);
            color: #fff; font-family: 'Cairo', sans-serif;
            font-size: 15px; font-weight: 600;
            outline: none; transition: all 0.25s;
        }

        .invoice-header .ih-customer input::placeholder { color: rgba(255,255,255,0.3); }

        .invoice-header .ih-customer input:focus {
            border-color: var(--primary);
            background: rgba(20,184,166,0.06);
            box-shadow: 0 0 0 3px rgba(20,184,166,0.15);
        }

        /* === بطاقة أصناف الفاتورة === */
        .items-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            overflow: hidden; margin-bottom: 20px;
            animation: fadeUp 0.5s ease 0.1s both;
        }

        .items-header {
            padding: 18px 24px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }

        .items-header h3 {
            font-size: 15px; font-weight: 700; color: var(--text-dark);
            display: flex; align-items: center; gap: 8px;
        }

        .items-header h3 .ih-badge {
            background: var(--primary-light); color: var(--primary-dark);
            font-size: 11px; padding: 2px 10px; border-radius: 6px;
            font-weight: 700;
        }

        .btn-add-row {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px;
            background: var(--primary-light); color: var(--primary-dark);
            border: none; border-radius: 8px;
            font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 700;
            cursor: pointer; transition: all 0.2s;
        }

        .btn-add-row:hover { background: var(--primary); color: #fff; }

        /* جدول الأصناف */
        .items-table-wrap { overflow-x: auto; }

        .items-table {
            width: 100%; border-collapse: collapse;
        }

        .items-table thead th {
            padding: 12px 16px; font-size: 11px; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: 0.8px; background: #f8fafc;
            border-bottom: 1.5px solid var(--border);
            text-align: right; white-space: nowrap;
        }

        .items-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
            animation: rowIn 0.3s ease both;
        }

        @keyframes rowIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .items-table tbody tr:hover { background: rgba(20,184,166,0.02); }

        .items-table tbody td {
            padding: 12px 16px; vertical-align: middle;
        }

        .items-table .row-num {
            font-size: 12px; font-weight: 700; color: var(--text-muted);
            width: 40px; text-align: center;
        }

        .items-table select,
        .items-table input[type="number"] {
            padding: 9px 12px; border: 1.5px solid var(--border);
            border-radius: 8px; font-family: 'Cairo', sans-serif;
            font-size: 13px; color: var(--text-dark);
            background: var(--card-bg); outline: none;
            transition: border-color 0.2s;
        }

        .items-table select:focus,
        .items-table input[type="number"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(20,184,166,0.08);
        }

        .items-table select {
            min-width: 200px; appearance: none; cursor: pointer;
            padding-left: 32px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: left 10px center;
        }

        .items-table input[type="number"] {
            width: 100px; text-align: center;
            direction: ltr; font-variant-numeric: tabular-nums;
        }

        .items-table input[type="number"].input-readonly {
            background: #f8fafc; color: var(--text-muted); cursor: default;
        }

        .subtotal-cell {
            font-weight: 700; color: var(--text-dark);
            font-size: 14px; min-width: 110px;
            text-align: center; font-variant-numeric: tabular-nums;
            direction: ltr;
        }

        .btn-remove-row {
            width: 32px; height: 32px; border-radius: 8px;
            border: 1px solid var(--border); background: transparent;
            color: var(--danger); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; transition: all 0.2s;
        }

        .btn-remove-row:hover { background: var(--danger-light); border-color: var(--danger); }

        /* حالة فارغة */
        .items-empty {
            text-align: center; padding: 48px 20px;
        }

        .items-empty i { font-size: 40px; color: var(--border); margin-bottom: 12px; }
        .items-empty p { font-size: 13px; color: var(--text-muted); }

        /* === ملخص الفاتورة === */
        .invoice-summary {
            display: flex; justify-content: flex-end; margin-bottom: 24px;
            animation: fadeUp 0.5s ease 0.2s both;
        }

        .summary-box {
            width: 340px; background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .summary-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border);
        }

        .summary-row:last-child { border-bottom: none; }

        .summary-row .sr-label { font-size: 13px; color: var(--text-body); }
        .summary-row .sr-value {
            font-size: 14px; font-weight: 600; color: var(--text-dark);
            font-variant-numeric: tabular-nums; direction: ltr;
        }

        .summary-row.sr-total {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 18px 24px;
        }

        .summary-row.sr-total .sr-label { color: rgba(255,255,255,0.9); font-weight: 600; font-size: 15px; }
        .summary-row.sr-total .sr-value { color: #fff; font-size: 22px; font-weight: 800; }

        .summary-row.sr-items .sr-value { color: var(--info); }

        /* === أزرار الإجراءات === */
        .form-actions {
            display: flex; align-items: center; gap: 12px;
            animation: fadeUp 0.5s ease 0.3s both;
        }

        .btn-submit {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 36px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all 0.25s;
            box-shadow: 0 2px 10px rgba(20,184,166,0.25);
        }

        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(20,184,166,0.35); }
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .btn-cancel {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 24px; background: transparent; color: var(--text-body);
            border: 1.5px solid var(--border); border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all 0.2s;
        }

        .btn-cancel:hover { background: var(--page-bg); border-color: var(--text-muted); }

        /* Toast إشعار */
        .toast {
            position: fixed; top: 24px; left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background: var(--danger); color: #fff;
            padding: 14px 24px; border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            z-index: 300; display: flex; align-items: center; gap: 10px;
            transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
        }

        .toast.show { transform: translateX(-50%) translateY(0); }

        /* استجابة */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
            .invoice-header { padding: 24px 20px; }
            .invoice-header .ih-top { flex-direction: column; align-items: flex-start; gap: 12px; }
            .invoice-header .ih-customer input { max-width: 100%; }
            .items-table select { min-width: 150px; }
            .items-table input[type="number"] { width: 80px; }
            .summary-box { width: 100%; }
            .form-actions { flex-direction: column; }
            .form-actions .btn-submit, .form-actions .btn-cancel { width: 100%; justify-content: center; }
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

    <!-- Toast للتحذيرات -->
    <div class="toast" id="toast">
        <i class="fas fa-exclamation-triangle"></i>
        <span id="toastMsg"></span>
    </div>

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
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/sale" class="nav-link active"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a></div>
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
                        <a href="<?php echo URL_ROOT; ?>/sale/index">المبيعات</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>فاتورة جديدة</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-body">

            <form action="<?php echo URL_ROOT; ?>/sale/create" method="POST" id="invoiceForm" novalidate>

                <!-- رأس الفاتورة -->
                <div class="invoice-header">
                    <div class="ih-top">
                        <div class="ih-title">
                            <div class="ih-icon"><i class="fas fa-file-invoice"></i></div>
                            <div>
                                <h2>فاتورة مبيعات جديدة</h2>
                                <p>أضف الأصناف واحسب الإجمالي ثم احفظ</p>
                            </div>
                        </div>
                        <div class="invoice-number">
                            <i class="fas fa-hashtag" style="margin-left:4px;"></i>
                            <?php echo 'INV-' . date('YmdHis'); ?>
                        </div>
                    </div>
                    <div class="ih-customer">
                        <label>اسم العميل</label>
                        <input type="text" name="customer_name" id="customerName" placeholder="أدخل اسم العميل أو اتركه لعميل نقدي" required>
                    </div>
                </div>

                <!-- أصناف الفاتورة -->
                <div class="items-card">
                    <div class="items-header">
                        <h3>
                            <i class="fas fa-list-check" style="color:var(--primary);"></i>
                            أصناف الفاتورة
                            <span class="ih-badge" id="itemsCount">0</span>
                        </h3>
                        <button type="button" class="btn-add-row" id="addRowBtn">
                            <i class="fas fa-plus"></i> إضافة صنف
                        </button>
                    </div>

                    <div id="itemsContainer">
                        <table class="items-table" id="itemsTable" style="display:none;">
                            <thead>
                                <tr>
                                    <th style="width:40px;text-align:center;">#</th>
                                    <th>المنتج</th>
                                    <th style="text-align:center;">السعر</th>
                                    <th style="text-align:center;">الكمية</th>
                                    <th style="text-align:center;">الإجمالي</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                        <div class="items-empty" id="itemsEmpty">
                            <i class="fas fa-cart-plus"></i>
                            <p>اضغط على "إضافة صنف" لبدء بناء الفاتورة</p>
                        </div>
                    </div>
                </div>

                <!-- ملخص الفاتورة -->
                <div class="invoice-summary">
                    <div class="summary-box">
                        <div class="summary-row sr-items">
                            <span class="sr-label">عدد الأصناف</span>
                            <span class="sr-value" id="sumItems">0</span>
                        </div>
                        <div class="summary-row">
                            <span class="sr-label">إجمالي الكميات</span>
                            <span class="sr-value" id="sumQty">0</span>
                        </div>
                        <div class="summary-row sr-total">
                            <span class="sr-label">الإجمالي النهائي</span>
                            <span class="sr-value" id="sumTotal">0.00 ر.س</span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit" id="btnSubmit">
                        <i class="fas fa-check-circle"></i>
                        حفظ الفاتورة
                    </button>
                    <a href="<?php echo URL_ROOT; ?>/sale/index" class="btn-cancel">
                        <i class="fas fa-arrow-right"></i>
                        رجوع للقائمة
                    </a>
                </div>

            </form>

        </div>
    </div>

    <script>
        /* === بيانات المنتجات من PHP === */
        const productsData = <?php
            $prodsArr = [];
            foreach($data['products'] as $p) {
                $prodsArr[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'price' => (float) $p->price,
                    'quantity' => (int) $p->quantity,
                    'cat_name' => $p->cat_name ?? ''
                ];
            }
            echo json_encode($prodsArr, JSON_UNESCAPED_UNICODE);
        ?>;

        let rowCounter = 0;

        const itemsBody = document.getElementById('itemsBody');
        const itemsTable = document.getElementById('itemsTable');
        const itemsEmpty = document.getElementById('itemsEmpty');
        const itemsCount = document.getElementById('itemsCount');
        const sumItems = document.getElementById('sumItems');
        const sumQty = document.getElementById('sumQty');
        const sumTotal = document.getElementById('sumTotal');
        const addRowBtn = document.getElementById('addRowBtn');
        const invoiceForm = document.getElementById('invoiceForm');
        const btnSubmit = document.getElementById('btnSubmit');

        /* إضافة صف جديد */
        addRowBtn.addEventListener('click', function() {
            rowCounter++;
            const tr = document.createElement('tr');
            tr.setAttribute('data-row', rowCounter);
            tr.style.animationDelay = '0s';

            // بناء قائمة المنتجات
            let optionsHtml = '<option value="">-- اختر المنتج --</option>';
            productsData.forEach(function(p) {
                const stockInfo = p.quantity > 0 ? ' (' + p.quantity + ' قطعة)' : ' (نفذ!)';
                const disabled = p.quantity <= 0 ? ' disabled' : '';
                optionsHtml += '<option value="' + p.id + '"' + disabled + '>' +
                    p.name + ' — ' + p.sku + stockInfo +
                    '</option>';
            });

            tr.innerHTML =
                '<td class="row-num">' + rowCounter + '</td>' +
                '<td><select name="product_id[]" class="prod-select" required>' + optionsHtml + '</select></td>' +
                '<td><input type="number" name="price[]" class="price-input input-readonly" value="0.00" step="0.01" min="0" readonly required style="direction:ltr;text-align:center;"></td>' +
                '<td><input type="number" name="quantity[]" class="qty-input" value="1" min="1" step="1" required style="direction:ltr;text-align:center;"></td>' +
                '<td class="subtotal-cell">0.00 ر.س</td>' +
                '<td><button type="button" class="btn-remove-row" title="حذف الصنف"><i class="fas fa-xmark"></i></button></td>';

            itemsBody.appendChild(tr);

            // إظهار الجدول وإخفاء الحالة الفارغة
            itemsTable.style.display = '';
            itemsEmpty.style.display = 'none';

            // ربط الأحداث
            const select = tr.querySelector('.prod-select');
            const priceInput = tr.querySelector('.price-input');
            const qtyInput = tr.querySelector('.qty-input');
            const removeBtn = tr.querySelector('.btn-remove-row');

            select.addEventListener('change', function() {
                const prodId = parseInt(this.value);
                const prod = productsData.find(function(p) { return p.id === prodId; });
                if (prod) {
                    priceInput.value = prod.price.toFixed(2);
                    priceInput.classList.remove('input-readonly');
                    qtyInput.max = prod.quantity;
                    if (parseInt(qtyInput.value) > prod.quantity) qtyInput.value = prod.quantity;
                    recalcRow(tr);
                } else {
                    priceInput.value = '0.00';
                    priceInput.classList.add('input-readonly');
                    qtyInput.max = '';
                    recalcRow(tr);
                }
            });

            qtyInput.addEventListener('input', function() {
                const prodId = parseInt(select.value);
                const prod = productsData.find(function(p) { return p.id === prodId; });
                if (prod && parseInt(this.value) > prod.quantity) {
                    this.value = prod.quantity;
                    showToast('الكمية المطلوبة تتجاوز المخزون المتوفر (' + prod.quantity + ' قطعة)');
                }
                recalcRow(tr);
            });

            removeBtn.addEventListener('click', function() {
                tr.style.opacity = '0';
                tr.style.transform = 'translateX(20px)';
                tr.style.transition = 'all 0.25s ease';
                setTimeout(function() {
                    tr.remove();
                    updateSummary();
                    if (itemsBody.children.length === 0) {
                        itemsTable.style.display = 'none';
                        itemsEmpty.style.display = '';
                    }
                    // إعادة ترقيم الصفوف
                    renumberRows();
                }, 250);
            });

            updateSummary();
        });

        /* حساب صف واحد */
        function recalcRow(tr) {
            const price = parseFloat(tr.querySelector('.price-input').value) || 0;
            const qty = parseInt(tr.querySelector('.qty-input').value) || 0;
            const subtotal = price * qty;
            tr.querySelector('.subtotal-cell').textContent = subtotal.toFixed(2) + ' ر.س';
            updateSummary();
        }

        /* تحديث الملخص */
        function updateSummary() {
            const rows = itemsBody.querySelectorAll('tr');
            let totalItems = rows.length;
            let totalQty = 0;
            let totalAmount = 0;

            rows.forEach(function(tr) {
                const qty = parseInt(tr.querySelector('.qty-input').value) || 0;
                const price = parseFloat(tr.querySelector('.price-input').value) || 0;
                totalQty += qty;
                totalAmount += (price * qty);
            });

            itemsCount.textContent = totalItems;
            sumItems.textContent = totalItems;
            sumQty.textContent = totalQty;
            sumTotal.textContent = totalAmount.toLocaleString('ar-SA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
        }

        /* إعادة ترقيم */
        function renumberRows() {
            const rows = itemsBody.querySelectorAll('tr');
            rows.forEach(function(tr, i) {
                tr.querySelector('.row-num').textContent = i + 1;
            });
        }

        /* Toast إشعار */
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.classList.add('show');
            setTimeout(function() { toast.classList.remove('show'); }, 3500);
        }

        /* تحقق قبل الإرسال */
        invoiceForm.addEventListener('submit', function(e) {
            const rows = itemsBody.querySelectorAll('tr');
            const customerName = document.getElementById('customerName');

            if (!customerName.value.trim()) {
                e.preventDefault();
                customerName.focus();
                customerName.style.borderColor = 'var(--danger)';
                customerName.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
                showToast('يرجى إدخال اسم العميل');
                return;
            }

            // إعادة حدود الحقل
            customerName.style.borderColor = '';
            customerName.style.boxShadow = '';

            if (rows.length === 0) {
                e.preventDefault();
                showToast('يرجى إضافة صنف واحد على الأقل للفاتورة');
                return;
            }

            // التحقق من اختيار منتج لكل صف
            let valid = true;
            rows.forEach(function(tr) {
                const select = tr.querySelector('.prod-select');
                if (!select.value) {
                    valid = false;
                    select.style.borderColor = 'var(--danger)';
                    select.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.08)';
                } else {
                    select.style.borderColor = '';
                    select.style.boxShadow = '';
                }
            });

            if (!valid) {
                e.preventDefault();
                showToast('يرجى اختيار منتج لكل صف في الفاتورة');
                return;
            }

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري حفظ الفاتورة...';
        });

        /* قائمة الموبايل */
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>