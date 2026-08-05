<?php
// app/views/customers/view.php
 $c = $data['customer'];
 $invoices = $data['invoices'] ?? [];
 $payments = $data['payments'] ?? [];
 $totalPaid = $data['total_paid'] ?? 0;
 $flash = $data['flash'] ?? null;
 $totalPurchases = $c->total_purchases ?? 0;
 $outstanding = max($totalPurchases - $totalPaid, 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات العميل — <?php echo htmlspecialchars($c->name); ?></title>
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
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }
        .sidebar-user .su-logout {
            color: var(--text-muted); font-size: 14px; padding: 6px;
            border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto;
        }
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

        .cust-profile-header {
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            border-radius: var(--radius); padding: 36px 40px;
            color: #fff; margin-bottom: 24px;
            position: relative; overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }

        .cph-top {
            display: flex; align-items: center; gap: 24px;
            position: relative; z-index: 2;
            flex-wrap: wrap;
        }

        .cph-avatar {
            width: 88px; height: 88px; border-radius: 22px;
            background: linear-gradient(135deg, var(--info), #0891b2);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: 800; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(6,182,212,0.25);
        }

        .cph-info h2 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .cph-info .cph-email { font-size: 13px; color: #94a3b8; margin-top: 2px; display: flex; align-items: center; gap: 6px; }
        .cph-info .cph-type {
            display: inline-flex; align-items: center; gap: 5px; margin-top: 10px;
            padding: 4px 14px; border-radius: 8px;
            font-size: 11px; font-weight: 700;
            background: rgba(255,255,255,0.1);
        }

        .cph-stats {
            display: flex; gap: 24px; position: relative; z-index: 2;
            margin-top: 20px; flex-wrap: wrap;
        }

        .cph-stat {
            background: rgba(255,255,255,0.06);
            border-radius: 12px; padding: 14px 20px;
            text-align: center;
            min-width: 120px;
        }
        .cph-stat-val { font-size: 22px; font-weight: 800; font-variant-numeric: tabular-nums; }
        .cph-stat-label { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        .content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
        .card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.15s both; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-header h3 { font-size: 15px; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }
        .card-body.np { padding: 0; }

        .inv-table { width: 100%; border-collapse: collapse; }
        .inv-table thead th { padding: 12px 18px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; background: #f8fafc; border-bottom: 1.5px solid var(--border); text-align: right; white-space: nowrap; }
        .inv-table tbody td { padding: 12px 18px; font-size: 13px; border-bottom: 1px solid var(--border); }
        .inv-table tbody tr:last-child { border-bottom: none; }
        .inv-table tbody tr:hover { background: rgba(20,184,166,0.02); }

        .inv-num { font-family: monospace; direction: ltr; font-size: 11px; color: var(--primary-dark); font-weight: 600; background: var(--primary-light); padding: 3px 10px; border-radius: 6px; white-space: nowrap; display: inline-block; }
        .inv-amount { font-weight: 700; color: var(--text-dark); font-variant-numeric: tabular-nums; direction: ltr; display: inline-block; }
        .inv-amount .curr { font-size: 11px; font-weight: 500; color: var(--text-muted); margin-right: 2px; }
        .inv-date { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
        .inv-date i { font-size: 11px; }
        .inv-status { display: inline-flex; align-items: center; gap: 5px; padding: 3px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .inv-status.st-paid { background: var(--success-light); color: #15803d; }
        .inv-status.st-unpaid { background: var(--accent-light); color: #b45309; }

        .act-btn { width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 13px; transition: all 0.2s; text-decoration: none; color: var(--text-body); }
        .act-btn.btn-view { color: var(--primary); }
        .act-btn.btn-view:hover { background: var(--primary-light); border-color: var(--primary); }
        .act-btn.btn-print { color: var(--text-muted); }
        .act-btn.btn-print:hover { background: var(--page-bg); border-color: var(--text-muted); }

        .pay-table { width: 100%; border-collapse: collapse; }
        .pay-table thead th { padding: 12px 18px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; background: #f8fafc; border-bottom: 1.5px solid var(--border); text-align: right; white-space: nowrap; }
        .pay-table tbody td { padding: 12px 18px; font-size: 13px; border-bottom: 1px solid var(--border); }
        .pay-table tbody tr:last-child { border-bottom: none; }

        .pay-amount { font-weight: 700; color: var(--success); font-variant-numeric: tabular-nums; direction: ltr; display: inline-block; }
        .pay-amount .curr { font-size: 11px; font-weight: 500; color: var(--text-muted); margin-right: 2px; }

        .pay-method { display: inline-flex; align-items: center; gap: 6px; padding: 3px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .pm-cash { background: var(--success-light); color: #15803d; }
        .pm-bank { background: var(--info-light); color: #0e7490; }
        .pm-check { background: var(--accent-light); color: #b45309; }
        .pm-card { background: var(--purple-light); color: #6d28d9; }

        .no-invoices { text-align: center; padding: 40px 20px; color: var(--text-muted); font-size: 13px; }
        .no-invoices i { font-size: 32px; display: block; margin-bottom: 10px; color: var(--border); }

        .balance-box {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            padding: 24px; margin-top: 24px;
            animation: fadeUp 0.5s ease 0.25s both;
        }

        .balance-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .balance-row:last-child { border-bottom: none; }

        .br-label { font-size: 13px; color: var(--text-muted); }
        .br-value { font-size: 16px; font-weight: 700; color: var(--text-dark); font-variant-numeric: tabular-nums; direction: ltr; }
        .br-value.positive { color: var(--success); }
        .br-value.negative { color: var(--danger); }
        .br-value.zero { color: var(--text-muted); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; } .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .content-grid { grid-template-columns: 1fr; }
            .cph-top { flex-direction: column; align-items: flex-start; }
            .cph-stats { gap: 12px; }
            .cph-stat { min-width: 90px; }
            .balance-box { margin-top: 20px; }
        }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.show { display: block; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }

        @media print {
            .sidebar, .topbar, .sidebar-overlay { display: none !important; }
            .main-content { margin-right: 0 !important; }
            .page-body { padding: 0 !important; background: #fff !important; }
            .card, .balance-box { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
            .inv-status { border: 1px solid #ccc !important; }
            .pay-method { border: 1px solid #ccc !important; }
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
            <div class="nav-section-title" style="margin-top:12px;">العلاقات</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/customer" class="nav-link active"><i class="fas fa-address-book"></i><span>العملاء</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/supplier" class="nav-link"><i class="fas fa-truck-field"></i><span>الموردين</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/report" class="nav-link"><i class="fas fa-chart-line"></i><span>التقارير</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">النظام</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/settings" class="nav-link"><i class="fas fa-gear"></i><span>الإعدادات</span></a></div>
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
                    <div class="page-title">بيانات العميل</div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <a href="<?php echo URL_ROOT; ?>/customer/index">العملاء</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span><?php echo htmlspecialchars($c->name); ?></span>
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="topbar-btn" title="تصدير PDF" onclick="window.print()"><i class="fas fa-file-pdf"></i></button>
                <button class="topbar-btn" title="طباعة" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">

            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- رأس العميل -->
            <div class="cust-profile-header">
                <div class="cph-top">
                    <div class="cph-avatar"><?php echo mb_substr($c->name, 0, 2); ?></div>
                    <div class="cph-info">
                        <h2><?php echo htmlspecialchars($c->name); ?></h2>
                        <div class="cph-email"><i class="far fa-envelope"></i> <?php echo htmlspecialchars($c->email ?? '—'); ?></div>
                        <div class="cph-type"><i class="fas fa-<?php echo $c->type === 'company' ? 'building' : 'user'; ?>"></i> <?php echo $c->type === 'company' ? 'شركة' : 'فرد'; ?></div>
                    </div>
                </div>
                <div class="cph-stats">
                    <div class="cph-stat">
                        <div class="cph-stat-val"><?php echo (int)($c->invoice_count ?? 0); ?></div>
                        <div class="cph-stat-label">فاتورة</div>
                    </div>
                    <div class="cph-stat">
                        <div class="cph-stat-val"><?php echo number_format($c->total_purchases ?? 0, 0); ?></div>
                        <div class="cph-stat-label">مشتريات (ر.س)</div>
                    </div>
                    <div class="cph-stat">
                        <div class="cph-stat-val" style="color:<?php echo $c->balance > 0 ? 'var(--danger)' : ($c->balance < 0 ? 'var(--success)' : 'var(--text-muted)'); ?>;">
                            <?php echo number_format(abs($c->balance), 2); ?>
                        </div>
                        <div class="cph-stat-label">رصيد مدين (ر.س)</div>
                    </div>
                </div>
            </div>

            <!-- شبكة الفواتير + المدفوعات -->
            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-file-invoice" style="color:var(--primary);"></i> فواتير العميل</h3>
                        <span style="font-size:12px;color:var(--text-muted);"><?php echo count($invoices); ?> فاتورة</span>
                    </div>
                    <div class="card-body np">
                        <?php if (!empty($invoices)) : ?>
                        <table class="inv-table">
                            <thead>
                                <tr><th>رقم الفاتورة</th><th>الإجمالي</th><th>التاريخ</th><th>الحالة</th><th></th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $inv) :
                                    $isPaid = isset($inv->payment_status) && $inv->payment_status === 'paid';
                                ?>
                                <tr>
                                    <td><span class="inv-num"><?php echo htmlspecialchars($inv->invoice_number); ?></span></td>
                                    <td><span class="inv-amount"><?php echo number_format($inv->total_amount, 2); ?><span class="curr">ر.س</span></span></td>
                                    <td><span class="inv-date"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($inv->created_at)); ?></span></td>
                                    <td>
                                        <?php if ($isPaid) : ?>
                                        <span class="inv-status st-paid"><i class="fas fa-circle-check"></i> مدفوعة</span>
                                        <?php else : ?>
                                        <span class="inv-status st-unpaid"><i class="fas fa-clock"></i> غير مدفوعة</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo URL_ROOT; ?>/sale/view/<?php echo $inv->id; ?>" class="act-btn btn-view" title="عرض"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else : ?>
                        <div class="no-invoices"><i class="fas fa-receipt"></i><p>لا توجد فواتير لهذا العميل بعد</p></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-money-bill-wave" style="color:var(--success);"></i> المدفوعات</h3>
                        <span style="font-size:12px;color:<?php echo $totalPaid > 0 ? 'var(--success)' : 'var(--text-muted)'; ?>; font-weight:700;"><?php echo number_format($totalPaid, 2); ?> ر.س</span>
                    </div>
                    <div class="card-body np">
                        <?php if (!empty($payments)) : ?>
                        <table class="pay-table">
                            <thead>
                                <tr><th>التاريخ</th><th>البيان</th><th>المبلغ</th><th>الطريقة</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $p) : ?>
                                <tr>
                                    <td><span class="inv-date"><i class="far fa-calendar"></i> <?php echo date('Y-m-d H:i', strtotime($p->created_at)); ?></span></td>
                                    <td><?php echo htmlspecialchars($p->notes ?? '—'); ?></td>
                                    <td><span class="pay-amount"><span class="curr">ر.س</span> <?php echo number_format($p->amount, 2); ?></span></td>
                                    <td>
                                        <?php
                                        $method = $p->method ?? 'cash';
                                        $methodLabel = ['cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي', 'check' => 'شيك', 'card' => 'بطاقة ائتمان'];
                                        $methodIcon = ['cash' => 'fa-money-bill', 'bank_transfer' => 'fa-building-columns', 'check' => 'fa-file-invoice-dollar', 'card' => 'fa-credit-card'];
                                        $methodClass = ['cash' => 'pm-cash', 'bank_transfer' => 'pm-bank', 'check' => 'pm-check', 'card' => 'pm-card'];
                                        $m = $methodLabel[$method] ?? $method;
                                        $mc = $methodClass[$method] ?? 'pm-cash';
                                        $mi = $methodIcon[$method] ?? 'fa-money-bill';
                                    ?>
                                    <span class="pay-method <?php echo $mc; ?>"><i class="fas <?php echo $mi; ?>"></i> <?php echo $m; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else : ?>
                        <div class="no-invoices"><i class="fas fa-check-circle"></i><p>لا توجد مدفوعات مسجلة بعد</p></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ملخص الرصيد -->
            <div class="balance-box">
                <div class="balance-row">
                    <span class="br-label"><i class="fas fa-calculator" style="margin-left:6px;"></i> إجمالي الفواتير</span>
                    <span class="br-value"><?php echo number_format($c->total_purchases ?? 0, 2); ?> <span class="curr">ر.س</span></span>
                </div>
                <div class="balance-row">
                    <span class="br-label"><i class="fas fa-arrow-down" style="margin-left:6px;"></i> إجمالي المدفوعات</span>
                    <span class="br-value positive"><?php echo number_format($totalPaid, 2); ?> <span class="curr">ر.س</span></span>
                </div>
                <div class="balance-row">
                    <span class="br-label"><i class="fas fa-wallet" style="margin-left:6px;"></i> الرصيد المدين</span>
                    <span class="br-value <?php echo $outstanding > 0 ? 'negative' : 'zero'; ?>"><?php echo number_format($outstanding, 2); ?> <span class="curr">ر.س</span></span>
                </div>
            </div>

        </div>
    </div>

    <script>
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
    </script>
</body>
</html>