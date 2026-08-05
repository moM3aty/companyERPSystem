<?php
// app/views/layouts/main.php

// ========================================
// استدعاء المتغيرات من المتحكم (مهم: يجب أن يكون أول سطر)
// ========================================
extract($data);

// ========================================
// التحقق من انتهاء الجلسة قبل أي إخراج
// ========================================
\Session::checkTimeout();

// ========================================
// الحصول على مسار الصفحة الحالية للمقارنة مع عناصر القائمة النشط
// ========================================
 $currentUrl = $_GET['url'] ?? 'dashboard';

// ========================================
// توليد عناصر القائمة من Layout
// ========================================
 $sidebarHtml = Layout::renderSidebar($currentUrl);

// ========================================
// توليد الشريط العلوي
// ========================================
 $breadcrumb = $breadcrumb ?? [
    ['label' => 'الرئيسية', 'url' => 'dashboard']
];

 $topbarHtml = Layout::renderTopbar($page_title ?? 'لوحة التحكم', $breadcrumb);

// ========================================
// توليد رسالة التنبيه (إن وُجدت)
// ========================================
 $flashHtml = Layout::renderFlash();

// ========================================
// بدء محتوى الصفحة
// ========================================
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'نظام ERP'; ?></title>
    
    <!-- الخطوط -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Chart.js للصفحات التي تحتاجه رسوم بيانية -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    
    <style>
        /* ==========================================
           المتغيرات CSS - مشتركة بين كل الصفحات
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
            --info-light: #cstring-fe;
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

        /* إعادة تعيين القيم الافتراضية */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--page-bg);
            color: var(--text-body);
            min-height: 100vh;
            overflow-x: hidden;
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
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
        }

        .sidebar-brand .s-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand .s-name {
            font-size: 17px;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -0.3px;
        }

        .sidebar-brand .s-name span {
            color: var(--primary);
        }

        .sidebar-brand .s-ver {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: -2px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        /* عنوان قسم في القائمة */
        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 12px 14px 8px;
        }

        /* عنصر قائمة */
        .nav-item {
            margin-bottom: 2px;
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
            transition: color 0.2s;
        }

        .nav-link:hover {
            background: #1e293b;
            color: #e2e8f0;
        }

        /* العنصر النشط */
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

        /* شارة على العنصر النشط */
        .nav-badge {
            margin-right: auto;
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            min-width: 22px;
            text-align: center;
        }

        /* مستخدم الشريط الجانبي */
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        }

        .sidebar-user .su-logout:hover {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }

        /* ==========================================
           المحتوى الرئيسي
           ========================================== */
        .main-content {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
            transition: margin 0.3s ease;
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

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* مسار التنقل */
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

        /* أزرار الشريط العلوي */
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

        /* زر القائمة للموبايل */
        .mobile-menu-btn {
            display: none;
        }

        /* حقل البحث في الشريط العلوي */
        .topbar-search {
            position: relative;
        }

        .topbar-search input {
            width: 240px;
            padding: 9px 14px 9px 38px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif;
            font-size: 13px;
            background: var(--card-bg);
            color: var(--text-dark);
            outline: none;
            transition: all 0.2s;
        }

        .topbar-search input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.08);
        }

        .topbar-search input::placeholder {
            color: var(--text-muted);
        }

        .topbar-search i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
            pointer-events: none;
        }

        /* ==========================================
           جسم الصفحة
           ========================================== */
        .page-body {
            padding: 28px 32px 40px;
        }

        /* ==========================================
           رسائل التنبيه (Flash Messages)
           ========================================== */
        .flash-msg {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 24px;
            animation: slideDown 0.4s ease both;
            border: 1px solid transparent;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
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

        .flash-msg.flash-warning {
            background: var(--accent-light);
            color: #b45309;
            border-color: #fde68a;
        }

        .flash-msg i {
            font-size: 16px;
        }

        /* ==========================================
           طباعة
           ========================================== */
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
            .table-card,
            .finance-card,
            .content-grid {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }
        }

        /* ==========================================
           استجابة الشاشات (الموبايل)
           ========================================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        .sidebar-overlay.show {
            display: block;
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
            .topbar-search {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

    <!-- طبقة التعتيم -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- الشريط الجانبي -->
    <aside class="sidebar" id="sidebar">
        <!-- الشعار -->
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text">
                <span class="s-name">ERP <span>Pro</span></span>
                <span class="s-ver">v<?php echo APP_VERSION; ?></span>
            </div>
        </div>

        <!-- القائمة -->
        <?php echo $sidebarHtml; ?>

        <!-- مستخدم الشريط -->
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo Session::getInitials(); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo Session::getUserName(); ?></div>
                <div class="su-role"><?php echo Session::getUserRole(); ?></div>
            </div>
            <a href="<?php echo Layout::url('auth/logout'); ?>" class="su-logout" title="تسجيل الخروج">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <!-- الشريط العلوي -->
        <?php echo $topbarHtml; ?>

        <!-- رسالة التنبيه -->
        <?php echo $flashHtml; ?>

        <!-- محتوى الصفحة الفعلي -->
        <?php echo $content; ?>

    </div>

    <!-- سكريبت الشريط الجانبي للموبايل -->
    <script>
        /* ==========================================
           إظهار/إخفاء القائمة في الموبايل
           ========================================== */
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

        /* إغلاق القائمة عند تغيير الحجم */
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
        });

        /* ==========================================
           البحث العام السريع
           ========================================== */
        const globalSearch = document.getElementById('globalSearch');

        if (globalSearch) {
            globalSearch.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length >= 2) {
                        // توجيه لصفحة البحث العام
                        window.location.href = '<?php echo Layout::url('search/results'); ?>?q=' + encodeURIComponent(query);
                    }
                }
            });
        }
    </script>
</body>
</html>