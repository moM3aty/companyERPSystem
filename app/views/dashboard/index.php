<?php
$pageTitle = $data['title'] ?? 'لوحة التحكم';
$stats = $data['stats'] ?? [];
$activities = $data['recent_activities'] ?? [];
$currentUrl = 'dashboard';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | ERP Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* (تم استخدام نفس متغيرات التصميم والتنسيق لضمان تجربة مستخدم موحدة) */
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

        /* Sidebar */
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
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
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

        /* Main */
        .main-content {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
        }

        .topbar {
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
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

        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, #0d9488 100%);
            border-radius: var(--radius);
            padding: 32px 40px;
            color: #fff;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
            box-shadow: 0 10px 30px rgba(20, 184, 166, 0.2);
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -150px;
            left: -100px;
        }

        .wb-content {
            position: relative;
            z-index: 2;
        }

        .wb-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .wb-subtitle {
            font-size: 15px;
            opacity: 0.9;
            max-width: 600px;
            line-height: 1.6;
        }

        /* Widget Grid */
        .widget-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
            animation: fadeUp 0.5s ease 0.1s both;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 24px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            transition: transform 0.3s;
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-details {
            flex: 1;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 4px;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* Secondary Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            animation: fadeUp 0.5s ease 0.2s both;
        }

        .dashboard-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .dc-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dc-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dc-body {
            padding: 24px;
        }

        /* Activity List */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .act-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--success-light);
            color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .act-details {
            flex: 1;
        }

        .act-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 2px;
        }

        .act-time {
            font-size: 11px;
            color: var(--text-muted);
        }

        .act-amount {
            font-size: 14px;
            font-weight: 800;
            color: var(--success);
            direction: ltr;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .qa-btn {
            padding: 14px;
            border-radius: 12px;
            background: var(--page-bg);
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }

        .qa-btn i {
            font-size: 20px;
            color: var(--primary);
        }

        .qa-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary-dark);
        }
    </style>
</head>

<body>

    <!-- Sidebar Inclusion (Simulated) -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text"><span class="s-name">ERP <span>Pro</span></span></div>
        </div>
        <!-- We use the dynamic Layout render function if available -->
        <?php if (class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div class="page-title">لوحة التحكم السريعة</div>
            <div style="font-weight:600; font-size:14px;"><i class="far fa-clock"></i> <?php echo date('Y-m-d'); ?></div>
        </header>

        <div class="page-body">

            <div class="welcome-banner">
                <div class="wb-content">
                    <h1 class="wb-title">مرحباً بك مجدداً، <?php echo htmlspecialchars($data['user']['name'] ?? 'مدير النظام'); ?> 👋</h1>
                    <p class="wb-subtitle">لقد تم الانتهاء من بناء هيكلية نظام ERP Pro بنجاح! جميع الوحدات من الموارد البشرية، المبيعات، المشتريات والمحاسبة جاهزة للعمل ضمن قاعدة البيانات الموحدة.</p>
                </div>
            </div>

            <div class="widget-grid">
                <!-- Widget 1 -->
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--info-light); color:var(--info);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo $stats['employees'] ?? 0; ?></div>
                        <div class="stat-label">الموظفين المسجلين</div>
                    </div>
                </div>
                <!-- Widget 2 -->
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--accent-light); color:var(--accent);">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo $stats['products'] ?? 0; ?></div>
                        <div class="stat-label">المنتجات بالمخزون</div>
                    </div>
                </div>
                <!-- Widget 3 -->
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--success-light); color:var(--success);">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo number_format($stats['sales'] ?? 0); ?></div>
                        <div class="stat-label">إجمالي المبيعات (ر.س)</div>
                    </div>
                </div>
                <!-- Widget 4 -->
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--purple-light); color:var(--purple);">
                        <i class="fas fa-diagram-project"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo $stats['projects'] ?? 0; ?></div>
                        <div class="stat-label">المشاريع النشطة</div>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <!-- النشاطات الحديثة -->
                <div class="dashboard-card">
                    <div class="dc-header">
                        <h3><i class="fas fa-bolt" style="color:var(--primary);"></i> أحدث فواتير المبيعات</h3>
                    </div>
                    <div class="dc-body">
                        <div class="activity-list">
                            <?php if (!empty($activities)): ?>
                                <?php foreach ($activities as $act): ?>
                                    <div class="activity-item">
                                        <div class="act-icon"><i class="fas fa-file-invoice"></i></div>
                                        <div class="act-details">
                                            <div class="act-title">فاتورة مبيعات #<?php echo htmlspecialchars($act->title); ?></div>
                                            <div class="act-time"><i class="far fa-clock"></i> <?php echo date('Y-m-d H:i', strtotime($act->created_at)); ?></div>
                                        </div>
                                        <div class="act-amount">+<?php echo number_format($act->details, 2); ?> ر.س</div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align:center; padding: 20px; color:var(--text-muted); font-size:13px;">
                                    <i class="fas fa-receipt" style="font-size:32px; margin-bottom:10px; color:var(--border);"></i><br>
                                    لا توجد فواتير مبيعات مسجلة بعد.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- إجراءات سريعة -->
                <div class="dashboard-card">
                    <div class="dc-header">
                        <h3><i class="fas fa-bolt" style="color:var(--accent);"></i> إجراءات سريعة</h3>
                    </div>
                    <div class="dc-body">
                        <div class="quick-actions">
                            <a href="<?php echo URL_ROOT; ?>/sale/create" class="qa-btn">
                                <i class="fas fa-file-invoice-dollar"></i> فاتورة مبيعات
                            </a>
                            <a href="<?php echo URL_ROOT; ?>/purchase/create" class="qa-btn">
                                <i class="fas fa-cart-plus"></i> أمر شراء
                            </a>
                            <a href="<?php echo URL_ROOT; ?>/product/create" class="qa-btn">
                                <i class="fas fa-box-open"></i> منتج جديد
                            </a>
                            <a href="<?php echo URL_ROOT; ?>/employee/create" class="qa-btn">
                                <i class="fas fa-user-plus"></i> إضافة موظف
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>