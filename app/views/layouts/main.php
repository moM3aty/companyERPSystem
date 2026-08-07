<?php
// app/views/layouts/main.php

// 1. جلب إعدادات الشركة (اللوجو والاسم)
$db = Database::getInstance();
$db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('company_name', 'company_logo')");
$sysSettings = $db->resultSet();
$companyName = 'ERP Pro';
$companyLogo = '';
foreach ($sysSettings as $s) {
    if ($s->setting_key === 'company_name' && !empty($s->setting_value)) $companyName = $s->setting_value;
    if ($s->setting_key === 'company_logo' && !empty($s->setting_value)) $companyLogo = URLROOT . $s->setting_value;
}

// 2. جلب الإشعارات الخاصة بالمستخدم الحالي
$unreadNotifs = [];
$notifCount = 0;
if (Session::isLoggedIn()) {
    require_once APP_ROOT . '/app/models/Notification.php';
    $notifModel = new Notification();
    $unreadNotifs = $notifModel->getUnread(Session::getUserId(), 10);
    $notifCount = count($unreadNotifs);
}

$title = $data['title'] ?? 'ERP Pro';
$userName = Session::getUserName();
$userRole = Session::getUserRole();
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

function isActive($uri, $paths)
{
    foreach ($paths as $path) {
        if (strpos($uri, $path) !== false) return true;
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - <?php echo htmlspecialchars($companyName); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/assets/css/style.css">
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --primary-light: #e0f2fe;
            --secondary: #64748b;
            --accent: #f59e0b;
            --success: #10b981;
            --success-light: #d1fae5;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --warning: #f59e0b;
            --info: #3b82f6;
            --purple: #8b5cf6;

            --bg-color: #f1f5f9;
            --page-bg: #f8fafc;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-text: #94a3b8;
            --text-main: #334155;
            --text-muted: #64748b;
            --text-dark: #0f172a;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--page-bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
        }

        ul {
            list-style: none;
        }

        .font-monospace {
            font-family: 'Fira Code', monospace !important;
        }

        /* ================= Sidebar ================= */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: -4px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        .sidebar-header {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: sticky;
            top: 0;
            background: var(--sidebar-bg);
            z-index: 10;
        }

        .sidebar-header .logo-container {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-header .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar-header .logo-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
        }

        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-menu {
            padding: 15px 10px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            color: var(--sidebar-text);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
        }

        .nav-link-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link-content i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--sidebar-hover);
            color: #fff;
        }

        .nav-link.active {
            background-color: rgba(14, 165, 233, 0.15);
            color: var(--primary);
        }

        .nav-link.active .nav-link-content i {
            color: var(--primary);
        }

        .arrow {
            font-size: 12px;
            transition: transform 0.3s;
        }

        .nav-link.open .arrow {
            transform: rotate(180deg);
        }

        .submenu {
            display: none;
            background: rgba(0, 0, 0, 0.2);
            border-radius: var(--radius-sm);
            margin-top: 4px;
            padding: 8px 0;
            border-right: 2px solid var(--primary-dark);
        }

        .submenu.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        .sub-link {
            padding: 10px 40px 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sub-link:hover,
        .sub-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .sub-link i {
            font-size: 8px;
            color: var(--primary);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ================= Main Content & Topbar ================= */
        .main-content {
            flex: 1;
            margin-right: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--bg-color);
        }

        .topbar {
            height: 70px;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 900;
            border-bottom: 1px solid var(--border);
        }

        .topbar-right,
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Notifications */
        .notif-dropdown {
            position: relative;
        }

        .notif-btn {
            position: relative;
            background: #f8fafc;
            border: 1px solid var(--border);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--text-dark);
            cursor: pointer;
            transition: 0.2s;
        }

        .notif-btn:hover {
            background: #e2e8f0;
        }

        .notif-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--danger);
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        .notif-menu {
            position: absolute;
            top: 55px;
            left: 0;
            width: 320px;
            background: #fff;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            display: none;
            flex-direction: column;
            overflow: hidden;
            transform-origin: top left;
            animation: scaleIn 0.2s ease;
        }

        .notif-menu.show {
            display: flex;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .notif-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border);
            font-weight: bold;
            color: var(--text-dark);
            display: flex;
            justify-content: space-between;
        }

        .notif-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border);
            display: block;
            transition: 0.2s;
            background: #fdfdfd;
        }

        .notif-item:hover {
            background: #f1f5f9;
        }

        .notif-item.unread {
            background: var(--info-light);
            border-left: 3px solid var(--info);
        }

        .notif-title {
            font-size: 13px;
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }

        .notif-msg {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .notif-empty {
            padding: 30px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 12px;
            border-radius: 30px;
            border: 1px solid var(--border);
            background: #f8fafc;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--primary-light);
            color: var(--primary-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: capitalize;
        }

        .logout-btn {
            color: var(--danger);
            font-size: 18px;
            padding: 8px;
            border-radius: 50%;
            transition: 0.2s;
        }

        .logout-btn:hover {
            background: var(--danger-light);
        }

        .content-area {
            padding: 32px;
            flex: 1;
        }

        /* ================= Utilities (لضمان عمل الصفحات السابقة) ================= */
        .card {
            background: #fff;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .card-body {
            padding: 24px;
        }

        .card-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            background: var(--page-bg);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
            font-size: 14px;
        }

        .required {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
            color: var(--text-dark);
        }

        .btn-success {
            background-color: var(--success);
            color: #fff;
        }

        .btn-danger {
            background-color: var(--danger);
            color: #fff;
        }

        .btn-warning {
            background-color: var(--warning);
            color: #fff;
        }

        .btn-info {
            background-color: var(--info);
            color: #fff;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 16px;
            text-align: right;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .table th {
            background-color: #f8fafc;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 12px;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            align-items: center;
            gap: 4px;
        }

        .badge-success {
            background-color: var(--success-light);
            color: #065f46;
        }

        .badge-danger {
            background-color: var(--danger-light);
            color: #991b1b;
        }

        .badge-warning {
            background-color: var(--accent-light);
            color: #92400e;
        }

        .badge-info {
            background-color: var(--info-light);
            color: #1e40af;
        }

        .badge-primary {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .badge-secondary {
            background-color: var(--border);
            color: var(--text-main);
        }

        .badge-purple {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .alert {
            padding: 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 14px;
        }

        .alert-success {
            background-color: var(--success-light);
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background-color: var(--danger-light);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            background: transparent;
        }

        .btn-icon.edit {
            color: var(--info);
            background: var(--info-light);
        }

        .btn-icon.edit:hover {
            background: var(--info);
            color: #fff;
        }

        .btn-icon.delete {
            color: var(--danger);
            background: var(--danger-light);
        }

        .btn-icon.delete:hover {
            background: var(--danger);
            color: #fff;
        }

        .btn-icon.view {
            color: var(--primary);
            background: var(--primary-light);
        }

        .btn-icon.view:hover {
            background: var(--primary);
            color: #fff;
        }

        .text-primary {
            color: var(--primary) !important;
        }

        .text-success {
            color: var(--success) !important;
        }

        .text-danger {
            color: var(--danger) !important;
        }

        .text-warning {
            color: var(--warning) !important;
        }

        .text-info {
            color: var(--info) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .text-dark {
            color: var(--text-dark) !important;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        @media print {

            .sidebar,
            .topbar,
            .d-print-none {
                display: none !important;
            }

            .main-content {
                margin-right: 0 !important;
            }

            .content-area {
                padding: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- القائمة الجانبية (Sidebar) الأكورديون -->
    <aside class="sidebar d-print-none">
        <div class="sidebar-header">
            <div class="logo-container">
                <?php if ($companyLogo): ?>
                    <img src="<?php echo $companyLogo; ?>" alt="Logo">
                <?php else: ?>
                    <div class="logo-icon"><i class="fas fa-cubes"></i></div>
                <?php endif; ?>
            </div>
            <h2><?php echo htmlspecialchars($companyName); ?></h2>
        </div>

        <nav class="nav-menu">
            <div class="nav-item">
                <a href="<?php echo URLROOT; ?>/dashboard/index" class="nav-link <?php echo isActive($currentUri, ['/dashboard']) ? 'active' : ''; ?>">
                    <div class="nav-link-content"><i class="fas fa-chart-pie"></i> <span>لوحة القيادة</span></div>
                </a>
            </div>

            <?php $salesActive = isActive($currentUri, ['/customer', '/lead', '/opportunity', '/quote', '/sale', '/campaign', '/followup']); ?>
            <div class="nav-item has-dropdown">
                <div class="nav-link dropdown-toggle <?php echo $salesActive ? 'active open' : ''; ?>">
                    <div class="nav-link-content"><i class="fas fa-bullseye"></i> <span>المبيعات و CRM</span></div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu <?php echo $salesActive ? 'active' : ''; ?>">
                    <li><a href="<?php echo URLROOT; ?>/customer/index" class="sub-link <?php echo isActive($currentUri, ['/customer']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> العملاء</a></li>
                    <li><a href="<?php echo URLROOT; ?>/lead/index" class="sub-link <?php echo isActive($currentUri, ['/lead']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> العملاء المحتملين</a></li>
                    <li><a href="<?php echo URLROOT; ?>/opportunity/index" class="sub-link <?php echo isActive($currentUri, ['/opportunity']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الفرص البيعية</a></li>
                    <li><a href="<?php echo URLROOT; ?>/followup/index" class="sub-link <?php echo isActive($currentUri, ['/followup']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> المتابعات والمهام</a></li>
                    <li><a href="<?php echo URLROOT; ?>/quote/index" class="sub-link <?php echo isActive($currentUri, ['/quote']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> عروض الأسعار</a></li>
                    <li><a href="<?php echo URLROOT; ?>/sale/index" class="sub-link <?php echo isActive($currentUri, ['/sale/index', '/sale/create', '/sale/show']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> فواتير المبيعات</a></li>
                    <li><a href="<?php echo URLROOT; ?>/saleReturn/index" class="sub-link <?php echo isActive($currentUri, ['/saleReturn']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> مرتجعات المبيعات</a></li>
                    <li><a href="<?php echo URLROOT; ?>/campaign/index" class="sub-link <?php echo isActive($currentUri, ['/campaign']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الحملات التسويقية</a></li>
                </ul>
            </div>

            <?php $purchasesActive = isActive($currentUri, ['/supplier', '/purchase', '/product', '/category', '/stocktake', '/warehouse']); ?>
            <div class="nav-item has-dropdown">
                <div class="nav-link dropdown-toggle <?php echo $purchasesActive ? 'active open' : ''; ?>">
                    <div class="nav-link-content"><i class="fas fa-boxes-stacked"></i> <span>المشتريات والمخازن</span></div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu <?php echo $purchasesActive ? 'active' : ''; ?>">
                    <li><a href="<?php echo URLROOT; ?>/supplier/index" class="sub-link <?php echo isActive($currentUri, ['/supplier']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الموردين</a></li>
                    <li><a href="<?php echo URLROOT; ?>/purchaseRequest/index" class="sub-link <?php echo isActive($currentUri, ['/purchaseRequest']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> طلبات الشراء (PR)</a></li>
                    <li><a href="<?php echo URLROOT; ?>/purchase/index" class="sub-link <?php echo isActive($currentUri, ['/purchase/index', '/purchase/create', '/purchase/edit', '/purchase/show', '/purchase/receive']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> أوامر الشراء (PO)</a></li>
                    <li><a href="<?php echo URLROOT; ?>/purchaseReturn/index" class="sub-link <?php echo isActive($currentUri, ['/purchaseReturn']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> مرتجعات المشتريات</a></li>
                    <li><a href="<?php echo URLROOT; ?>/category/index" class="sub-link <?php echo isActive($currentUri, ['/category']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> التصنيفات</a></li>
                    <li><a href="<?php echo URLROOT; ?>/product/index" class="sub-link <?php echo isActive($currentUri, ['/product']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> المنتجات والمخزون</a></li>
                    <li><a href="<?php echo URLROOT; ?>/stocktake/index" class="sub-link <?php echo isActive($currentUri, ['/stocktake']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> تسويات الجرد</a></li>
                </ul>
            </div>

            <?php $financeActive = isActive($currentUri, ['/accounting', '/account', '/treasury', '/payment', '/expense', '/journal']); ?>
            <div class="nav-item has-dropdown">
                <div class="nav-link dropdown-toggle <?php echo $financeActive ? 'active open' : ''; ?>">
                    <div class="nav-link-content"><i class="fas fa-calculator"></i> <span>المالية والمحاسبة</span></div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu <?php echo $financeActive ? 'active' : ''; ?>">
                    <li><a href="<?php echo URLROOT; ?>/accounting/dashboard" class="sub-link <?php echo isActive($currentUri, ['/accounting/dashboard']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> اللوحة المالية</a></li>
                    <li><a href="<?php echo URLROOT; ?>/account/tree" class="sub-link <?php echo isActive($currentUri, ['/account/tree', '/account/create', '/account/edit']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> دليل الحسابات</a></li>
                    <li><a href="<?php echo URLROOT; ?>/treasury/index" class="sub-link <?php echo isActive($currentUri, ['/treasury']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الصندوق والبنوك</a></li>
                    <li><a href="<?php echo URLROOT; ?>/journal/index" class="sub-link <?php echo isActive($currentUri, ['/journal']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> القيود اليومية</a></li>
                    <li><a href="<?php echo URLROOT; ?>/payment/index" class="sub-link <?php echo isActive($currentUri, ['/payment']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> المقبوضات والمدفوعات</a></li>
                    <li><a href="<?php echo URLROOT; ?>/expense/index" class="sub-link <?php echo isActive($currentUri, ['/expense']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> المصروفات التشغيلية</a></li>
                    <li><a href="<?php echo URLROOT; ?>/accounting/trialBalance" class="sub-link <?php echo isActive($currentUri, ['/accounting/trialBalance']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> ميزان المراجعة</a></li>
                    <li><a href="<?php echo URLROOT; ?>/accounting/balanceSheet" class="sub-link <?php echo isActive($currentUri, ['/accounting/balanceSheet']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الميزانية العمومية</a></li>
                    <li><a href="<?php echo URLROOT; ?>/accounting/incomeStatement" class="sub-link <?php echo isActive($currentUri, ['/accounting/incomeStatement']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> قائمة الدخل</a></li>
                </ul>
            </div>

            <?php $hrActive = isActive($currentUri, ['/employee', '/attendance', '/leave', '/advance', '/sanction', '/payroll', '/appraisal']); ?>
            <div class="nav-item has-dropdown">
                <div class="nav-link dropdown-toggle <?php echo $hrActive ? 'active open' : ''; ?>">
                    <div class="nav-link-content"><i class="fas fa-users-gear"></i> <span>الموارد البشرية</span></div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu <?php echo $hrActive ? 'active' : ''; ?>">
                    <li><a href="<?php echo URLROOT; ?>/employee/index" class="sub-link <?php echo isActive($currentUri, ['/employee/index', '/employee/create', '/employee/edit']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> شؤون الموظفين</a></li>
                    <li><a href="<?php echo URLROOT; ?>/employeeContract/index" class="sub-link <?php echo isActive($currentUri, ['/employeeContract']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> عقود العمل</a></li>
                    <li><a href="<?php echo URLROOT; ?>/attendance/index" class="sub-link <?php echo isActive($currentUri, ['/attendance']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الحضور والانصراف</a></li>
                    <li><a href="<?php echo URLROOT; ?>/leave/index" class="sub-link <?php echo isActive($currentUri, ['/leave']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> طلبات الإجازات</a></li>
                    <li><a href="<?php echo URLROOT; ?>/advance/index" class="sub-link <?php echo isActive($currentUri, ['/advance']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> السلف والعهد</a></li>
                    <li><a href="<?php echo URLROOT; ?>/sanction/index" class="sub-link <?php echo isActive($currentUri, ['/sanction']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الجزاءات والمخالفات</a></li>
                    <li><a href="<?php echo URLROOT; ?>/payroll/index" class="sub-link <?php echo isActive($currentUri, ['/payroll']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> مسيرات الرواتب</a></li>
                    <li><a href="<?php echo URLROOT; ?>/appraisal/index" class="sub-link <?php echo isActive($currentUri, ['/appraisal']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> تقييم الأداء</a></li>
                </ul>
            </div>

            <?php $pmActive = isActive($currentUri, ['/project', '/contract', '/fixedAsset', '/timesheet']); ?>
            <div class="nav-item has-dropdown">
                <div class="nav-link dropdown-toggle <?php echo $pmActive ? 'active open' : ''; ?>">
                    <div class="nav-link-content"><i class="fas fa-diagram-project"></i> <span>المشاريع والأصول</span></div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu <?php echo $pmActive ? 'active' : ''; ?>">
                    <li><a href="<?php echo URLROOT; ?>/project/index" class="sub-link <?php echo isActive($currentUri, ['/project', '/timesheet']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> إدارة المشاريع</a></li>
                    <li><a href="<?php echo URLROOT; ?>/contract/index" class="sub-link <?php echo isActive($currentUri, ['/contract']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> إدارة العقود</a></li>
                    <li><a href="<?php echo URLROOT; ?>/fixedAsset/index" class="sub-link <?php echo isActive($currentUri, ['/fixedAsset']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الأصول الثابتة</a></li>
                </ul>
            </div>

            <?php $sysActive = isActive($currentUri, ['/user', '/document', '/ticket', '/report', '/activityLog', '/settings']); ?>
            <div class="nav-item has-dropdown">
                <div class="nav-link dropdown-toggle <?php echo $sysActive ? 'active open' : ''; ?>">
                    <div class="nav-link-content"><i class="fas fa-cogs"></i> <span>الإدارة والدعم</span></div>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu <?php echo $sysActive ? 'active' : ''; ?>">
                    <li><a href="<?php echo URLROOT; ?>/document/index" class="sub-link <?php echo isActive($currentUri, ['/document']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> الأرشيف والوثائق</a></li>
                    <li><a href="<?php echo URLROOT; ?>/ticket/index" class="sub-link <?php echo isActive($currentUri, ['/ticket']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> تذاكر الدعم الفني</a></li>
                    <li><a href="<?php echo URLROOT; ?>/report/index" class="sub-link <?php echo isActive($currentUri, ['/report']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> التقارير الذكية</a></li>
                    <?php if ($userRole === 'admin'): ?>
                        <li><a href="<?php echo URLROOT; ?>/user/index" class="sub-link <?php echo isActive($currentUri, ['/user']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> إدارة المستخدمين</a></li>
                        <li><a href="<?php echo URLROOT; ?>/activityLog/index" class="sub-link <?php echo isActive($currentUri, ['/activityLog']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> سجل التدقيق (Audit)</a></li>
                        <li><a href="<?php echo URLROOT; ?>/settings/index" class="sub-link <?php echo isActive($currentUri, ['/settings']) ? 'active' : ''; ?>"><i class="fas fa-circle"></i> إعدادات النظام</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar d-print-none">
            <div class="topbar-right">
                <div class="page-title fw-bold text-dark fs-5">
                    <?php echo htmlspecialchars($title); ?>
                </div>
            </div>

            <div class="topbar-left">
                <!-- الإشعارات -->
                <div class="notif-dropdown">
                    <button class="notif-btn" id="notifToggle">
                        <i class="far fa-bell"></i>
                        <?php if ($notifCount > 0): ?>
                            <span class="notif-badge"><?php echo $notifCount; ?></span>
                        <?php endif; ?>
                    </button>

                    <div class="notif-menu" id="notifMenu">
                        <div class="notif-header">
                            <span>الإشعارات الجديدة</span>
                            <span class="badge badge-danger"><?php echo $notifCount; ?></span>
                        </div>
                        <div class="notif-list">
                            <?php if (!empty($unreadNotifs)): foreach ($unreadNotifs as $note): ?>
                                    <a href="<?php echo URLROOT; ?>/dashboard/readNotification/<?php echo $note->id; ?>" class="notif-item unread">
                                        <div class="notif-title">
                                            <?php echo htmlspecialchars($note->title); ?>
                                            <i class="fas fa-circle text-info" style="font-size:8px;"></i>
                                        </div>
                                        <div class="notif-msg"><?php echo htmlspecialchars($note->message); ?></div>
                                        <div class="text-muted" style="font-size:10px; margin-top:4px;"><i class="far fa-clock"></i> <?php echo date('m-d H:i', strtotime($note->created_at)); ?></div>
                                    </a>
                                <?php endforeach;
                            else: ?>
                                <div class="notif-empty">
                                    <i class="far fa-bell-slash" style="font-size:24px; margin-bottom:8px; color:var(--border);"></i><br>
                                    لا توجد إشعارات جديدة حالياً
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ملف المستخدم -->
                <div class="user-profile">
                    <div class="user-info text-right">
                        <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                        <span class="user-role"><?php echo htmlspecialchars($userRole); ?></span>
                    </div>
                    <div class="user-avatar">
                        <?php echo mb_substr($userName, 0, 1); ?>
                    </div>
                </div>

                <a href="<?php echo URLROOT; ?>/auth/logout" class="logout-btn" title="تسجيل الخروج" onclick="return confirm('هل تريد تسجيل الخروج من النظام؟');">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </header>

        <div class="content-area">
            <?php
            if (isset($_SESSION['flash'])) {
                $flashInfo = $_SESSION['flash'];
                $icon = $flashInfo['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
                echo '<div class="alert alert-' . $flashInfo['type'] . '"><i class="fas ' . $icon . '"></i> ' . $flashInfo['message'] . '</div>';
                unset($_SESSION['flash']);
            }
            ?>

            <?php echo $viewContent ?? ''; ?>

        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // سكريبت القائمة المنسدلة للـ Sidebar
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', function(e) {
                    const submenu = this.nextElementSibling;
                    const isOpen = this.classList.contains('open');

                    document.querySelectorAll('.submenu').forEach(menu => {
                        menu.classList.remove('active');
                        menu.previousElementSibling.classList.remove('open');
                    });

                    if (!isOpen) {
                        submenu.classList.add('active');
                        this.classList.add('open');
                    }
                });
            });

            // سكريبت إظهار الإشعارات
            const notifBtn = document.getElementById('notifToggle');
            const notifMenu = document.getElementById('notifMenu');

            if (notifBtn && notifMenu) {
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifMenu.classList.toggle('show');
                });

                document.addEventListener('click', function(e) {
                    if (!notifMenu.contains(e.target)) {
                        notifMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>

</html>