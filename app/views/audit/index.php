<?php
// app/views/audit/index.php
$pageTitle = $data['title'] ?? 'سجل التدقيق والأنشطة';
$logs = $data['logs'] ?? [];
$users = $data['users'] ?? [];
$actions = $data['actions'] ?? [];
$tables = $data['tables'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'audit/index';
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
        /* ==========================================
           المتغيرات الأساسية (مشتركة)
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

        /* القائمة الجانبية (Sidebar) */
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

        /* المحتوى الرئيسي */
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

        .flash-msg.flash-error {
            background: var(--danger-light);
            color: #dc2626;
            border-color: #fecaca;
        }

        /* أدوات الصفحة (Toolbar و الفلتر) */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            animation: fadeUp 0.5s ease both;
        }

        .toolbar-right h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #dc2626);
            box-shadow: 0 2px 10px rgba(239, 68, 68, 0.2);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }

        .filter-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 24px;
            background: var(--card-bg);
            padding: 16px 24px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            animation: fadeUp 0.5s ease 0.1s both;
        }

        .filter-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            width: 100%;
            align-items: center;
        }

        .filter-select {
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            outline: none;
            background: #f8fafc;
            color: var(--text-dark);
            cursor: pointer;
            min-width: 160px;
            transition: border-color 0.2s;
        }

        .filter-select:focus {
            border-color: var(--primary);
        }

        .btn-filter {
            padding: 10px 24px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(20, 184, 166, 0.2);
        }

        .btn-filter:hover {
            background: var(--primary-dark);
        }

        .btn-clear {
            padding: 10px 20px;
            background: transparent;
            color: var(--text-body);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-clear:hover {
            background: var(--page-bg);
        }

        /* الجدول */
        .table-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            animation: fadeUp 0.5s ease 0.2s both;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 14px 20px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8fafc;
            border-bottom: 1.5px solid var(--border);
            text-align: right;
            white-space: nowrap;
        }

        tbody tr {
            transition: background 0.15s;
            border-bottom: 1px solid var(--border);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: rgba(0, 0, 0, 0.01);
        }

        tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: var(--text-body);
            vertical-align: middle;
        }

        .json-preview {
            font-family: monospace;
            font-size: 12px;
            background: #f8fafc;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--border);
            display: inline-block;
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            direction: ltr;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-insert {
            background: var(--success-light);
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-update {
            background: var(--accent-light);
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-delete {
            background: var(--danger-light);
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .badge-login {
            background: var(--info-light);
            color: #0e7490;
            border: 1px solid #a5f3fc;
        }

        .badge-default {
            background: var(--border);
            color: var(--text-body);
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--border);
            margin-bottom: 16px;
            display: block;
        }

        .empty-state h4 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* المودال */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: var(--card-bg);
            border-radius: var(--radius);
            width: 420px;
            max-width: 90vw;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2);
            animation: modalIn 0.3s ease;
            overflow: hidden;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header {
            padding: 24px 24px 0;
            text-align: center;
        }

        .modal-header .modal-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--danger-light);
            color: var(--danger);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }

        .modal-header h3 {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .modal-header p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .modal-body {
            padding: 0 24px;
            text-align: center;
        }

        .modal-footer {
            padding: 20px 24px 24px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .modal-btn {
            padding: 10px 24px;
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .modal-btn.btn-cancel {
            background: var(--page-bg);
            color: var(--text-body);
            border: 1px solid var(--border);
        }

        .modal-btn.btn-cancel:hover {
            background: var(--border);
        }

        .modal-btn.btn-confirm {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
        }

        .modal-btn.btn-confirm:hover {
            background: #dc2626;
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
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 10px;
                border: 1px solid var(--border);
                background: transparent;
                color: var(--text-body);
                font-size: 16px;
                cursor: pointer;
            }

            .page-body {
                padding: 20px 16px;
            }

            .topbar {
                padding: 0 16px;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-select,
            .btn-filter,
            .btn-clear {
                flex: 1;
                min-width: 100%;
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
                <div class="su-name"><?php echo $_SESSION['user_name'] ?? 'مدير النظام'; ?></div>
                <div class="su-role"><?php echo $_SESSION['user_role'] ?? 'admin'; ?></div>
            </div>
            <a href="<?php echo URL_ROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $pageTitle; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>النظام</span>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>سجل التدقيق</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-body">

            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="toolbar">
                <div class="toolbar-right">
                    <h3><i class="fas fa-clipboard-list" style="color:var(--primary);"></i> سجل النشاطات (Audit Logs)</h3>
                </div>
                <div>
                    <button type="button" class="btn-action btn-danger" onclick="openCleanModal()">
                        <i class="fas fa-broom"></i> تنظيف السجل
                    </button>
                </div>
            </div>

            <div class="filter-bar">
                <form method="GET" action="<?php echo URL_ROOT; ?>/audit/index" class="filter-form">
                    <select name="user" class="filter-select">
                        <option value="">-- جميع المستخدمين --</option>
                        <?php foreach ($users as $u) : ?>
                            <option value="<?php echo $u->user_id; ?>" <?php echo (($data['filter_user'] ?? '') == $u->user_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="action" class="filter-select">
                        <option value="">-- جميع الإجراءات --</option>
                        <?php foreach ($actions as $act) : ?>
                            <option value="<?php echo htmlspecialchars($act->action); ?>" <?php echo (($data['filter_action'] ?? '') == $act->action) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($act->action); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="table" class="filter-select">
                        <option value="">-- جميع الجداول --</option>
                        <?php foreach ($tables as $tbl) : ?>
                            <option value="<?php echo htmlspecialchars($tbl->table_name); ?>" <?php echo (($data['filter_table'] ?? '') == $tbl->table_name) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tbl->table_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> تصفية النتائج</button>
                    <a href="<?php echo URL_ROOT; ?>/audit/index" class="btn-clear">إعادة تعيين</a>
                </form>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المستخدم</th>
                                <th>الإجراء</th>
                                <th>الجدول المستهدف</th>
                                <th>معرّف السجل</th>
                                <th>البيانات القديمة</th>
                                <th>البيانات الجديدة</th>
                                <th>التاريخ والوقت</th>
                                <th>تفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log) :
                                $actionClass = match (strtolower($log->action)) {
                                    'insert', 'create', 'add' => 'badge-insert',
                                    'update', 'edit', 'modify' => 'badge-update',
                                    'delete', 'remove' => 'badge-delete',
                                    'login', 'auth' => 'badge-login',
                                    default => 'badge-default'
                                };
                            ?>
                                <tr>
                                    <td style="color:var(--text-muted);font-weight:600;font-size:12px;"><?php echo $log->id; ?></td>
                                    <td style="font-weight:600;color:var(--text-dark);"><?php echo htmlspecialchars($log->user_name ?? 'غير معروف'); ?></td>
                                    <td><span class="badge <?php echo $actionClass; ?>"><?php echo htmlspecialchars($log->action); ?></span></td>
                                    <td style="font-family:monospace;"><?php echo htmlspecialchars($log->table_name); ?></td>
                                    <td style="font-weight:600;"><?php echo $log->record_id ?? '—'; ?></td>
                                    <td>
                                        <?php if ($log->old_data) : ?>
                                            <div class="json-preview"><?php echo htmlspecialchars(mb_substr($log->old_data, 0, 25)) . '...'; ?></div>
                                        <?php else : ?>
                                            <span style="color:var(--text-muted);">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($log->new_data) : ?>
                                            <div class="json-preview"><?php echo htmlspecialchars(mb_substr($log->new_data, 0, 25)) . '...'; ?></div>
                                        <?php else : ?>
                                            <span style="color:var(--text-muted);">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size:12px;color:var(--text-muted);"><i class="far fa-clock"></i> <?php echo date('Y-m-d H:i', strtotime($log->created_at)); ?></div>
                                    </td>
                                    <td>
                                        <a href="<?php echo URL_ROOT; ?>/audit/view/<?php echo $log->id; ?>" class="act-btn" title="عرض التفاصيل الكاملة"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($logs)) : ?>
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <i class="fas fa-clipboard-check"></i>
                                            <h4>لا توجد سجلات مطابقة</h4>
                                            <p>لم يتم العثور على أي نشاطات تطابق معايير البحث الحالية.</p>
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

    <!-- مودال تنظيف السجل -->
    <div class="modal-overlay" id="cleanModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon"><i class="fas fa-broom"></i></div>
                <h3>تنظيف سجل التدقيق</h3>
                <p>سيتم حذف السجلات القديمة لتخفيف الضغط على قاعدة البيانات. يرجى تحديد المدة الزمنية للاحتفاظ بالسجلات.</p>
            </div>
            <form method="POST" action="<?php echo URL_ROOT; ?>/audit/clean">
                <div class="modal-body">
                    <div style="display:flex;flex-direction:column;text-align:right;margin-bottom:16px;">
                        <label style="font-size:13px;font-weight:600;margin-bottom:8px;">حذف السجلات الأقدم من (بالأيام):</label>
                        <input type="number" name="days" value="30" min="7" required style="padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-family:inherit;font-size:14px;outline:none;direction:ltr;text-align:right;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn btn-cancel" onclick="closeCleanModal()">إلغاء</button>
                    <button type="submit" class="modal-btn btn-confirm">تأكيد الحذف</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const cleanModal = document.getElementById('cleanModal');

        function openCleanModal() {
            cleanModal.classList.add('show');
        }

        function closeCleanModal() {
            cleanModal.classList.remove('show');
        }
        cleanModal.addEventListener('click', function(e) {
            if (e.target === this) closeCleanModal();
        });

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