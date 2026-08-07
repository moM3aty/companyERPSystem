<?php
// app/views/suppliers/view.php
$pageTitle = $data['title'] ?? 'بيانات المورد';
$s = $data['supplier'] ?? null;
$purchaseOrders = $data['purchaseOrders'] ?? [];
$payments = $data['payments'] ?? [];
$totalPaid = $data['totalPaid'] ?? 0;
$totalPayables = $data['totalPayables'] ?? 0;
$outstanding = $data['outstanding'] ?? 0;
$flash = $data['flash'] ?? null;
$currentUrl = 'supplier/index';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات المورد — <?php echo htmlspecialchars($s->name ?? ''); ?></title>
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
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--page-bg);
            color: var(--text-body);
            min-height: 100vh;
        }

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
            border-left: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand {
            padding: 24px 24px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-brand .s-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-brand .s-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand .s-name {
            font-size: 17px;
            font-weight: 800;
            color: #f8fafc;
        }

        .sidebar-brand .s-name span {
            color: var(--primary);
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 12px 14px 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: var(--radius-sm);
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .nav-link:hover {
            background: #1e293b;
            color: #e2e8f0;
        }

        .nav-link.active {
            background: rgba(20, 184, 166, 0.1);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            right: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: var(--primary);
            border-radius: 0 4px 4px 0;
        }

        .sidebar-user {
            padding: 16px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-user .su-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .sidebar-user .su-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user .su-name {
            font-size: 13px;
            font-weight: 600;
            color: #e2e8f0;
        }

        .sidebar-user .su-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        .sidebar-user .su-logout {
            color: var(--text-muted);
            font-size: 14px;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
            margin-right: auto;
        }

        .sidebar-user .su-logout:hover {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }

        .main-content {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
            transition: margin 0.3s ease;
        }

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

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-body);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 15px;
        }

        .topbar-btn:hover {
            background: var(--page-bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .mobile-menu-btn {
            display: none;
        }

        .page-body {
            padding: 28px 32px 40px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .flash-msg {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 24px;
            animation: fadeUp 0.4s ease both;
            border: 1px solid transparent;
        }

        .flash-msg.flash-success {
            background: var(--success-light);
            color: #15803d;
            border-color: #bbf7d0;
        }

        .flash-msg.flash-warning {
            background: var(--accent-light);
            color: #b45309;
            border-color: #fde68a;
        }

        .supplier-profile-header {
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            border-radius: var(--radius);
            padding: 36px 40px;
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }

        .sph-top {
            display: flex;
            align-items: center;
            gap: 24px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .sph-avatar {
            width: 88px;
            height: 88px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--accent), #d97706);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25);
        }

        .sph-info h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .sph-info .sph-email {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sph-info .sph-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            padding: 4px 14px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.1);
        }

        .sph-stats {
            display: flex;
            gap: 24px;
            position: relative;
            z-index: 2;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .sph-stat {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 14px 20px;
            text-align: center;
            min-width: 120px;
        }

        .sph-stat-val {
            font-size: 22px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .sph-stat-label {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            animation: fadeUp 0.5s ease 0.15s both;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 0;
            overflow-x: auto;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table thead th {
            padding: 12px 18px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            background: #f8fafc;
            border-bottom: 1.5px solid var(--border);
            text-align: right;
            white-space: nowrap;
        }

        .details-table tbody td {
            padding: 14px 18px;
            font-size: 13.5px;
            border-bottom: 1px solid var(--border);
            color: var(--text-body);
        }

        .details-table tbody tr:last-child td {
            border-bottom: none;
        }

        .details-table tbody tr:hover {
            background: rgba(0, 0, 0, 0.01);
        }

        .po-num {
            font-family: monospace;
            font-size: 12px;
            color: var(--primary-dark);
            font-weight: 700;
            background: var(--primary-light);
            padding: 4px 10px;
            border-radius: 6px;
            direction: ltr;
            display: inline-block;
        }

        .amount-val {
            font-weight: 700;
            color: var(--text-dark);
            font-variant-numeric: tabular-nums;
            direction: ltr;
            display: inline-block;
        }

        .amount-val.success {
            color: var(--success);
        }

        .amount-val .curr {
            font-size: 11px;
            font-weight: 500;
            color: var(--text-muted);
            margin-right: 2px;
        }

        .date-val {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .po-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .po-status.st-delivered {
            background: var(--success-light);
            color: #15803d;
        }

        .po-status.st-pending {
            background: var(--accent-light);
            color: #b45309;
        }

        .po-status.st-cancelled {
            background: var(--danger-light);
            color: #dc2626;
        }

        .pay-method {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: #f1f5f9;
            color: var(--text-body);
            border: 1px solid var(--border);
        }

        .act-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            text-decoration: none;
            color: var(--primary);
        }

        .act-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary);
        }

        .empty-box {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .empty-box i {
            font-size: 32px;
            display: block;
            margin-bottom: 12px;
            color: var(--border);
        }

        .balance-box {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 24px;
            margin-top: 24px;
            animation: fadeUp 0.5s ease 0.25s both;
        }

        .balance-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .balance-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .balance-row:first-child {
            padding-top: 0;
        }

        .br-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-body);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .br-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            font-variant-numeric: tabular-nums;
            direction: ltr;
        }

        .br-value.positive {
            color: var(--success);
        }

        .br-value.negative {
            color: var(--danger);
        }

        .br-value.zero {
            color: var(--text-muted);
        }

        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .page-body {
                padding: 20px 16px;
            }

            .topbar {
                padding: 0 16px;
            }

            .sph-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .sph-stats {
                gap: 12px;
            }

            .sph-stat {
                min-width: 100px;
                flex: 1;
            }
        }

        @media print {

            .sidebar,
            .topbar,
            .sidebar-overlay {
                display: none !important;
            }

            .main-content {
                margin-right: 0 !important;
            }

            .page-body {
                padding: 0 !important;
                background: #fff !important;
            }

            .card,
            .balance-box,
            .supplier-profile-header {
                box-shadow: none !important;
                border: 1px solid #000 !important;
                break-inside: avoid;
            }

            body {
                background: #fff !important;
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text"><span class="s-name">ERP <span>Pro</span></span></div>
        </div>
        <?php if (class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
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
                        <a href="<?php echo URLROOT; ?>/supplier/index">إدارة الموردين</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>تفاصيل المورد</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <a href="<?php echo URLROOT; ?>/supplier/edit/<?php echo $s->id; ?>" class="topbar-btn" title="تعديل بيانات المورد"><i class="fas fa-pen"></i></a>
                <button class="topbar-btn" title="طباعة الكشف" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">

            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- رأس ملف المورد -->
            <div class="supplier-profile-header">
                <div class="sph-top">
                    <div class="sph-avatar"><?php echo mb_substr($s->name ?? 'م', 0, 2); ?></div>
                    <div class="sph-info">
                        <h2><?php echo htmlspecialchars($s->name ?? ''); ?></h2>
                        <div class="sph-email">
                            <i class="far fa-envelope"></i> <?php echo htmlspecialchars($s->email ?? '—'); ?>
                            &nbsp;|&nbsp; <i class="fas fa-phone" style="font-size:11px;"></i> <?php echo htmlspecialchars($s->phone ?? '—'); ?>
                        </div>
                        <div class="sph-type">
                            <i class="fas fa-<?php echo ($s->type ?? '') === 'company' ? 'building' : 'user'; ?>"></i>
                            <?php echo ($s->type ?? '') === 'company' ? 'شركة' : 'فرد'; ?>
                        </div>
                        <?php if (!empty($s->contact_person)) : ?>
                            <div class="sph-type" style="background:rgba(255,255,255,0.05); margin-right: 8px;">
                                <i class="fas fa-user-tie"></i> جهة الاتصال: <?php echo htmlspecialchars($s->contact_person); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="sph-stats">
                    <div class="sph-stat">
                        <div class="sph-stat-val"><?php echo (int)($s->po_count ?? 0); ?></div>
                        <div class="sph-stat-label">إجمالي الأوامر</div>
                    </div>
                    <div class="sph-stat">
                        <div class="sph-stat-val"><?php echo number_format($totalPayables, 0); ?></div>
                        <div class="sph-stat-label">المشتريات (ر.س)</div>
                    </div>
                    <div class="sph-stat" style="background: rgba(255,255,255,0.12);">
                        <div class="sph-stat-val" style="color:<?php echo $outstanding > 0 ? '#fca5a5' : '#e2e8f0'; ?>;">
                            <?php echo number_format($outstanding, 2); ?>
                        </div>
                        <div class="sph-stat-label">مستحق للمورد (ر.س)</div>
                    </div>
                </div>
            </div>

            <!-- شبكة الجداول -->
            <div class="content-grid">

                <!-- أوامر الشراء -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-file-invoice" style="color:var(--accent);"></i> أوامر الشراء</h3>
                        <span style="font-size:12px;color:var(--text-muted);font-weight:600;"><?php echo count($purchaseOrders); ?> طلب</span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($purchaseOrders)) : ?>
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>رقم الأمر</th>
                                        <th style="text-align:left;">الإجمالي</th>
                                        <th>الحالة</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchaseOrders as $po) :
                                        $statusClass = match ($po->status) {
                                            'delivered' => 'st-delivered',
                                            'pending', 'ordered', 'approved' => 'st-pending',
                                            'cancelled', 'rejected' => 'st-cancelled',
                                            default => 'st-pending'
                                        };
                                        $statusLabel = match ($po->status) {
                                            'delivered' => 'تم التسليم',
                                            'pending' => 'قيد الانتظار',
                                            'approved' => 'معتمد',
                                            'ordered' => 'تم الطلب',
                                            'cancelled' => 'ملغى',
                                            'rejected' => 'مرفوض',
                                            default => $po->status
                                        };
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="po-num"><?php echo htmlspecialchars($po->po_number); ?></span>
                                                <div class="date-val" style="margin-top:4px;"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($po->created_at)); ?></div>
                                            </td>
                                            <td style="text-align:left;"><span class="amount-val"><?php echo number_format($po->total_amount, 2); ?> <span class="curr">ر.س</span></span></td>
                                            <td>
                                                <span class="po-status <?php echo $statusClass; ?>">
                                                    <i class="fas fa-<?php echo $po->status === 'delivered' ? 'circle-check' : ($po->status === 'cancelled' ? 'xmark' : 'clock'); ?>"></i>
                                                    <?php echo $statusLabel; ?>
                                                </span>
                                            </td>
                                            <td style="text-align:left;">
                                                <a href="<?php echo URLROOT; ?>/purchase/view/<?php echo $po->id; ?>" class="act-btn" title="عرض الأمر"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="empty-box"><i class="fas fa-receipt"></i>
                                <p>لا توجد أوامر شراء سابقة لهذا المورد</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- مدفوعات المورد -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-money-bill-wave" style="color:var(--success);"></i> سجل الدفعات السابقة</h3>
                        <span style="font-size:12px;color:var(--success);font-weight:700;"><?php echo number_format($totalPaid, 2); ?> ر.س</span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($payments)) : ?>
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الطريقة</th>
                                        <th style="text-align:left;">المبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $p) :
                                        $method = $p->method ?? 'cash';
                                        $methodLabel = match ($method) {
                                            'cash' => 'نقدي',
                                            'bank_transfer' => 'تحويل بنكي',
                                            'check' => 'شيك',
                                            'card' => 'بطاقة',
                                            default => 'غير محدد'
                                        };
                                        $methodIcon = match ($method) {
                                            'cash' => 'fa-money-bill',
                                            'bank_transfer' => 'fa-building-columns',
                                            'check' => 'fa-file-signature',
                                            'card' => 'fa-credit-card',
                                            default => 'fa-wallet'
                                        };
                                    ?>
                                        <tr>
                                            <td><span class="date-val"><i class="far fa-clock"></i> <?php echo date('Y-m-d', strtotime($p->created_at)); ?></span></td>
                                            <td><span class="pay-method"><i class="fas <?php echo $methodIcon; ?>"></i> <?php echo $methodLabel; ?></span></td>
                                            <td style="text-align:left;"><span class="amount-val success"><?php echo number_format($p->amount, 2); ?> <span class="curr">ر.س</span></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="empty-box"><i class="fas fa-hand-holding-dollar"></i>
                                <p>لا توجد مدفوعات مسجلة حتى الآن</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div> <!-- .content-grid -->

            <!-- ملخص الحساب -->
            <div class="balance-box">
                <div class="balance-row">
                    <span class="br-label"><i class="fas fa-calculator" style="color:var(--text-muted);"></i> إجمالي المشتريات (أوامر الشراء)</span>
                    <span class="br-value"><?php echo number_format($totalPayables, 2); ?> <span style="font-size:12px;font-weight:500;color:var(--text-muted);">ر.س</span></span>
                </div>
                <div class="balance-row">
                    <span class="br-label"><i class="fas fa-arrow-down" style="color:var(--success);"></i> إجمالي ما تم دفعه</span>
                    <span class="br-value positive"><?php echo number_format($totalPaid, 2); ?> <span style="font-size:12px;font-weight:500;color:var(--text-muted);">ر.س</span></span>
                </div>
                <div class="balance-row" style="padding-top:16px; margin-top:4px; border-top:2px solid var(--border);">
                    <span class="br-label" style="font-size:16px; color:var(--text-dark);"><i class="fas fa-wallet" style="color:var(--danger);"></i> المستحق للمورد (الدائن)</span>
                    <span class="br-value <?php echo $outstanding > 0 ? 'negative' : 'zero'; ?>" style="font-size:24px;">
                        <?php echo number_format($outstanding, 2); ?> <span style="font-size:14px;font-weight:600;color:var(--text-muted);">ر.س</span>
                    </span>
                </div>
            </div>

        </div> <!-- .page-body -->
    </div> <!-- .main-content -->

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