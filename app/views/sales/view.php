<?php
// app/views/sales/view.php
 $inv = $data['invoice'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة رقم: <?php echo $inv->invoice_number; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #14b8a6; --primary-dark: #0d9488; --primary-light: #ccfbf1;
            --accent: #f59e0b; --accent-light: #fef3c7;
            --success: #22c55e; --danger: #ef4444;
            --info: #06b6d4; --purple: #8b5cf6;
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

        /* أزرار الطباعة */
        .print-actions {
            display: flex; gap: 10px; align-items: center;
        }

        .btn-print {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 20px; background: linear-gradient(135deg, var(--success), #16a34a);
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(34,197,94,0.2);
        }

        .btn-print:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(34,197,94,0.3); }

        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; background: transparent; color: var(--text-body);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all 0.2s;
        }

        .btn-back:hover { background: var(--page-bg); }

        /* === بطاقة الفاتورة === */
        .invoice-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-md);
            overflow: hidden; animation: fadeUp 0.5s ease both;
            max-width: 900px;
        }

        /* رأس الفاتورة */
        .inv-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            padding: 36px 40px; border-bottom: 2px solid var(--border);
            gap: 24px; flex-wrap: wrap;
        }

        .inv-brand { display: flex; align-items: center; gap: 14px; }

        .inv-brand-logo {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff;
        }

        .inv-brand-name { font-size: 22px; font-weight: 800; color: var(--text-dark); }
        .inv-brand-name span { color: var(--primary); }
        .inv-brand-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .inv-meta { text-align: left; direction: ltr; }

        .inv-meta-title {
            font-size: 28px; font-weight: 900; color: var(--primary-dark);
            font-family: monospace; letter-spacing: 1px;
        }

        .inv-meta-date {
            font-size: 12px; color: var(--text-muted); margin-top: 4px;
            direction: rtl; text-align: left;
        }

        .inv-status {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 8px; padding: 4px 14px; border-radius: 8px;
            font-size: 12px; font-weight: 700;
            background: var(--primary-light); color: var(--primary-dark);
            direction: rtl;
        }

        /* معلومات الطرفين */
        .inv-parties {
            display: grid; grid-template-columns: 1fr 1fr;
            padding: 28px 40px; border-bottom: 1px solid var(--border);
            gap: 32px;
        }

        .inv-party-label {
            font-size: 10px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px;
        }

        .inv-party-name {
            font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;
        }

        .inv-party-detail {
            font-size: 13px; color: var(--text-body); line-height: 1.8;
            display: flex; align-items: center; gap: 8px;
        }

        .inv-party-detail i { color: var(--text-muted); font-size: 12px; width: 16px; }

        /* جدول الأصناف */
        .inv-table-wrap { padding: 0 40px; }

        .inv-table {
            width: 100%; border-collapse: collapse; margin: 24px 0;
        }

        .inv-table thead th {
            padding: 14px 16px; font-size: 11px; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: 0.8px; background: #f8fafc;
            border-bottom: 2px solid var(--border); text-align: right;
        }

        .inv-table thead th:last-child,
        .inv-table thead th:nth-child(3),
        .inv-table thead th:nth-child(4) { text-align: center; }
        .inv-table thead th:first-child { text-align: center; width: 50px; }

        .inv-table tbody td {
            padding: 16px; font-size: 14px; color: var(--text-body);
            border-bottom: 1px solid var(--border);
        }

        .inv-table tbody td:first-child { text-align: center; color: var(--text-muted); font-weight: 600; font-size: 12px; }
        .inv-table tbody td:nth-child(3),
        .inv-table tbody td:nth-child(4) { text-align: center; font-variant-numeric: tabular-nums; direction: ltr; }
        .inv-table tbody td:last-child { text-align: center; font-weight: 700; color: var(--text-dark); font-variant-numeric: tabular-nums; direction: ltr; font-size: 15px; }

        .inv-table tbody tr:last-child td { border-bottom: none; }
        .inv-table tbody tr:hover { background: rgba(20,184,166,0.02); }

        /* ملخص الفاتورة */
        .inv-summary {
            padding: 0 40px 32px;
            display: flex; justify-content: flex-end;
        }

        .inv-summary-box { width: 320px; }

        .inv-sum-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 0; border-bottom: 1px solid #f1f5f9;
        }

        .inv-sum-row .isr-label { font-size: 13px; color: var(--text-body); }
        .inv-sum-row .isr-value { font-size: 14px; font-weight: 600; color: var(--text-dark); font-variant-numeric: tabular-nums; direction: ltr; }

        .inv-sum-row.isr-total {
            border-bottom: none; border-top: 2px solid var(--border);
            padding-top: 16px; margin-top: 4px;
        }

        .inv-sum-row.isr-total .isr-label { font-size: 16px; font-weight: 700; color: var(--text-dark); }
        .inv-sum-row.isr-total .isr-value { font-size: 24px; font-weight: 900; color: var(--primary-dark); }

        /* تذييل الفاتورة */
        .inv-footer {
            padding: 20px 40px; background: #f8fafc;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            font-size: 12px; color: var(--text-muted);
        }

        .inv-footer-stamp {
            display: flex; align-items: center; gap: 8px;
        }

        .inv-footer-stamp i { color: var(--success); font-size: 14px; }

        /* طباعة */
        @media print {
            .sidebar, .topbar, .print-actions, .sidebar-overlay { display: none !important; }
            .main-content { margin-right: 0 !important; }
            .page-body { padding: 0 !important; background: #fff !important; }
            .invoice-card { box-shadow: none !important; border: none !important; border-radius: 0 !important; max-width: 100% !important; }
            body { background: #fff !important; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
            .inv-header { padding: 24px 20px; }
            .inv-parties { padding: 20px; grid-template-columns: 1fr; }
            .inv-table-wrap { padding: 0 20px; }
            .inv-summary { padding: 0 20px 24px; }
            .inv-summary-box { width: 100%; }
            .inv-footer { padding: 16px 20px; flex-direction: column; gap: 8px; }
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
            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
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
                <button class="topbar-btn mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title">تفاصيل الفاتورة</div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <a href="<?php echo URL_ROOT; ?>/sale/index">المبيعات</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span><?php echo $inv->invoice_number; ?></span>
                    </div>
                </div>
            </div>
            <div class="print-actions">
                <a href="<?php echo URL_ROOT; ?>/sale/index" class="btn-back"><i class="fas fa-arrow-right"></i> رجوع</a>
                <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> طباعة الفاتورة</button>
            </div>
        </header>

        <div class="page-body">
            <div class="invoice-card">
                <!-- رأس الفاتورة -->
                <div class="inv-header">
                    <div class="inv-brand">
                        <div class="inv-brand-logo"><i class="fas fa-cubes"></i></div>
                        <div>
                            <div class="inv-brand-name">ERP <span>Pro</span></div>
                            <div class="inv-brand-sub">نظام إدارة موارد المؤسسة</div>
                        </div>
                    </div>
                    <div class="inv-meta">
                        <div class="inv-meta-title">INVOICE</div>
                        <div class="inv-meta-date"><?php echo date('Y-m-d', strtotime($inv->created_at)); ?></div>
                        <div class="inv-meta-date"><?php echo date('h:i A', strtotime($inv->created_at)); ?></div>
                        <div class="inv-status"><i class="fas fa-circle-check"></i> مكتملة</div>
                    </div>
                </div>

                <!-- معلومات الطرفين -->
                <div class="inv-parties">
                    <div>
                        <div class="inv-party-label">معلومات الفاتورة</div>
                        <div class="inv-party-detail"><i class="fas fa-hashtag"></i> رقم الفاتورة: <strong><?php echo $inv->invoice_number; ?></strong></div>
                        <div class="inv-party-detail"><i class="far fa-calendar"></i> التاريخ: <?php echo date('Y/m/d', strtotime($inv->created_at)); ?></div>
                        <div class="inv-party-detail"><i class="far fa-clock"></i> الوقت: <?php echo date('h:i A', strtotime($inv->created_at)); ?></div>
                    </div>
                    <div>
                        <div class="inv-party-label">بيانات العميل</div>
                        <div class="inv-party-name"><?php echo htmlspecialchars($inv->customer_name); ?></div>
                        <div class="inv-party-detail"><i class="fas fa-user"></i> عميل نقدي</div>
                    </div>
                </div>

                <!-- جدول الأصناف -->
                <div class="inv-table-wrap">
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th>م</th>
                                <th>المنتج</th>
                                <th>السعر</th>
                                <th>الكمية</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; $totalItems = 0; foreach($data['items'] as $item) : $totalItems += $item->quantity; ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td style="font-weight:600;color:var(--text-dark);"><?php echo htmlspecialchars($item->product_name); ?></td>
                                <td><?php echo number_format($item->price, 2); ?></td>
                                <td><?php echo $item->quantity; ?></td>
                                <td><?php echo number_format($item->subtotal, 2); ?> ر.س</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ملخص الفاتورة -->
                <div class="inv-summary">
                    <div class="inv-summary-box">
                        <div class="inv-sum-row">
                            <span class="isr-label">عدد الأصناف</span>
                            <span class="isr-value"><?php echo count($data['items']); ?></span>
                        </div>
                        <div class="inv-sum-row">
                            <span class="isr-label">إجمالي الكميات</span>
                            <span class="isr-value"><?php echo $totalItems; ?></span>
                        </div>
                        <div class="inv-sum-row isr-total">
                            <span class="isr-label">الإجمالي النهائي</span>
                            <span class="isr-value"><?php echo number_format($inv->total_amount, 2); ?> ر.س</span>
                        </div>
                    </div>
                </div>

                <!-- تذييل -->
                <div class="inv-footer">
                    <div class="inv-footer-stamp">
                        <i class="fas fa-circle-check"></i>
                        <span>تم إصدار هذه الفاتورة إلكترونياً عبر نظام ERP Pro</span>
                    </div>
                    <div>شكراً لتعاملكم معنا</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar'); const overlay = document.getElementById('sidebarOverlay'); const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>