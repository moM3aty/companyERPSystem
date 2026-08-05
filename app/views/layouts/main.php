<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'ERP Pro'); ?> | نظام إدارة الموارد</title>
    
    <!-- خطوط ورموز -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        /* ==========================================
           التصميم الموحد للنظام (Global Styles)
           ========================================== */
        :root {
            --primary: #14b8a6; --primary-dark: #0d9488; --primary-light: #ccfbf1;
            --accent: #f59e0b; --accent-light: #fef3c7;
            --success: #22c55e; --success-light: #dcfce7;
            --danger: #ef4444; --danger-light: #fee2e2;
            --info: #06b6d4; --info-light: #cffafe;
            --purple: #8b5cf6; --purple-light: #ede9fe;
            --sidebar-w: 280px; --topbar-h: 70px;
            --page-bg: #f8fafc; --card-bg: #ffffff;
            --text-dark: #0f172a; --text-body: #334155; --text-muted: #64748b;
            --border: #e2e8f0; --radius: 16px; --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05); --shadow-md: 0 4px 20px rgba(0,0,0,0.05);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; overflow-x: hidden; }

        /* ==========================================
           القائمة الجانبية (Sidebar) - تصميم محسّن
           ========================================== */
        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); z-index: 100; display: flex; flex-direction: column; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 1px solid rgba(255,255,255,0.05); box-shadow: -4px 0 25px rgba(0,0,0,0.1); }
        .sidebar-brand { padding: 20px 24px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.2); }
        .sidebar-brand .s-logo { width: 44px; height: 44px; background: linear-gradient(135deg, var(--primary), var(--info)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff; flex-shrink: 0; box-shadow: 0 4px 15px rgba(20,184,166,0.3); }
        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 20px; font-weight: 800; color: #f8fafc; letter-spacing: -0.5px; line-height: 1.2; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-brand .s-ver { font-size: 11px; color: var(--primary-light); font-weight: 600;}
        
        .sidebar-nav { flex: 1; padding: 20px 16px; overflow-y: auto; }
        .sidebar-nav::-webkit-scrollbar { width: 6px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        
        .nav-section-title { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; padding: 16px 14px 8px; margin-top: 8px; }
        .nav-section-title:first-child { margin-top: 0; padding-top: 0; }
        
        .nav-item { margin-bottom: 4px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; color: #cbd5e1; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; position: relative; overflow: hidden; }
        .nav-link i { width: 22px; text-align: center; font-size: 16px; transition: transform 0.2s; color: #64748b; }
        
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .nav-link:hover i { color: var(--primary-light); transform: scale(1.1); }
        
        .nav-link.active { background: linear-gradient(90deg, rgba(20,184,166,0.15) 0%, rgba(20,184,166,0.05) 100%); color: var(--primary-light); border-right: 4px solid var(--primary); }
        .nav-link.active i { color: var(--primary); }
        
        .nav-badge { margin-right: auto; background: var(--danger); color: #fff; font-size: 10px; padding: 2px 8px; border-radius: 20px; font-weight: 800; box-shadow: 0 2px 5px rgba(239,68,68,0.3); }

        .sidebar-user { padding: 16px 20px; background: rgba(0,0,0,0.25); border-top: 1px solid rgba(255, 255, 255, 0.05); display: flex; align-items: center; gap: 12px; }
        .sidebar-user .su-avatar { width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 15px; flex-shrink: 0; border: 2px solid rgba(255,255,255,0.1); }
        .sidebar-user .su-info { flex: 1; min-width: 0; }
        .sidebar-user .su-name { font-size: 14px; font-weight: 700; color: #f8fafc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user .su-role { font-size: 11px; color: var(--primary-light); font-weight: 600; }
        .sidebar-user .su-logout { color: #64748b; font-size: 16px; padding: 8px; border-radius: 10px; transition: all 0.2s; text-decoration: none; margin-right: auto; background: rgba(255,255,255,0.05); }
        .sidebar-user .su-logout:hover { color: #fff; background: var(--danger); box-shadow: 0 4px 10px rgba(239,68,68,0.3); }

        /* ==========================================
           منطقة المحتوى والشريط العلوي
           ========================================== */
        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; transition: margin 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; }
        
        .topbar { height: var(--topbar-h); background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .topbar-left-section { display: flex; align-items: center; gap: 20px; }
        .mobile-menu-btn { display: none; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: #fff; color: var(--text-dark); align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 16px; box-shadow: var(--shadow-sm); }
        .mobile-menu-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }
        
        .page-title-area { display: flex; flex-direction: column; }
        .page-title { font-size: 20px; font-weight: 800; color: var(--text-dark); line-height: 1.2; }
        
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); margin-top: 4px; font-weight: 600; }
        .breadcrumb a { color: var(--primary); text-decoration: none; transition: color 0.2s; }
        .breadcrumb a:hover { color: var(--primary-dark); text-decoration: underline; }
        .breadcrumb i { font-size: 9px; color: #cbd5e1; }

        .topbar-right-section { display: flex; align-items: center; gap: 12px; }
        .search-box { position: relative; }
        .search-box input { width: 250px; padding: 10px 16px 10px 40px; border: 1px solid var(--border); border-radius: 20px; font-family: 'Cairo', sans-serif; font-size: 13px; background: var(--page-bg); color: var(--text-dark); outline: none; transition: all 0.2s; }
        .search-box input:focus { width: 300px; border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(20,184,166,0.1); }
        .search-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        
        .topbar-btn { width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--border); background: #fff; color: var(--text-body); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 16px; position: relative; }
        .topbar-btn:hover { background: var(--page-bg); color: var(--primary); border-color: var(--primary); }
        .topbar-btn .badge { position: absolute; top: -2px; right: -2px; width: 18px; height: 18px; background: var(--danger); color: #fff; border-radius: 50%; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; }

        .page-body { padding: 32px; flex: 1; display: flex; flex-direction: column;}
        
        /* Animations */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        
        /* الرسائل والتنبيهات */
        .flash-wrapper { animation: slideDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both; margin-bottom: 24px; }
        .flash-msg { padding: 16px 20px; border-radius: 12px; display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; box-shadow: var(--shadow-sm); border-right: 4px solid; }
        .flash-success { background: #fff; color: var(--text-dark); border-right-color: var(--success); }
        .flash-success i { color: var(--success); font-size: 20px; }
        .flash-error { background: #fff; color: var(--text-dark); border-right-color: var(--danger); }
        .flash-error i { color: var(--danger); font-size: 20px; }
        .flash-warning { background: #fff; color: var(--text-dark); border-right-color: var(--accent); }
        .flash-warning i { color: var(--accent); font-size: 20px; }

        /* Mobile Adjustments */
        @media (max-width: 992px) {
            .search-box input { width: 200px; }
            .search-box input:focus { width: 200px; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .topbar { padding: 0 16px; height: 60px; }
            .page-title { font-size: 16px; }
            .search-box { display: none; }
            .page-body { padding: 20px 16px; }
        }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 99; backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s; }
        .sidebar-overlay.show { display: block; opacity: 1; }

        /* للطباعة */
        @media print {
            .sidebar, .topbar, .sidebar-overlay, .flash-wrapper { display: none !important; }
            .main-content { margin-right: 0 !important; }
            .page-body { padding: 0 !important; background: #fff !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body>

    <!-- غطاء الشاشة للجوال -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- الشريط الجانبي (Sidebar) -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-layer-group"></i></div>
            <div class="s-text">
                <div class="s-name">ERP <span>Pro</span></div>
                <div class="s-ver">الإصدار <?php echo htmlspecialchars($app_version ?? '2.0.0'); ?></div>
            </div>
        </div>
        
        <!-- القوائم الديناميكية من الـ Layout Class -->
        <?php echo $sidebarHtml ?? ''; ?>
        
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo htmlspecialchars($user_initials ?? 'م'); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo htmlspecialchars($user_name ?? 'مدير النظام'); ?></div>
                <div class="su-role"><?php echo htmlspecialchars($user_role ?? 'Administrator'); ?></div>
            </div>
            <a href="<?php echo URL_ROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-power-off"></i></a>
        </div>
    </aside>

    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        
        <!-- الشريط العلوي (Topbar) -->
        <header class="topbar">
            <div class="topbar-left-section">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة">
                    <i class="fas fa-bars-staggered"></i>
                </button>
                <div class="page-title-area">
                    <h1 class="page-title"><?php echo htmlspecialchars($page_title ?? 'لوحة التحكم'); ?></h1>
                    
                    <?php if (!empty($breadcrumb)) : ?>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard"><i class="fas fa-home"></i> الرئيسية</a>
                        <?php foreach ($breadcrumb as $crumb) : ?>
                            <i class="fas fa-chevron-left"></i>
                            <?php if(isset($crumb['url'])): ?>
                                <a href="<?php echo URL_ROOT . '/' . $crumb['url']; ?>"><?php echo htmlspecialchars($crumb['label']); ?></a>
                            <?php else: ?>
                                <span><?php echo htmlspecialchars($crumb['label']); ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>

            <div class="topbar-right-section">
                <div class="search-box">
                    <input type="text" placeholder="ابحث في النظام..." autocomplete="off">
                    <i class="fas fa-search"></i>
                </div>
                <button class="topbar-btn" title="الإشعارات" onclick="alert('وحدة الإشعارات سيتم تفعيلها لاحقاً')">
                    <i class="far fa-bell"></i>
                    <span class="badge">3</span>
                </button>
                <button class="topbar-btn" title="الإعدادات السريعة" onclick="window.location.href='<?php echo URL_ROOT; ?>/settings'">
                    <i class="fas fa-gear"></i>
                </button>
            </div>
        </header>

        <!-- منطقة محتوى الصفحة المتغير -->
        <main class="page-body">
            
            <!-- رسائل النظام (Flash Messages) -->
            <?php if (!empty($flash)) : ?>
                <div class="flash-wrapper">
                    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                        <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : ($flash['type'] === 'error' ? 'fa-xmark-circle' : 'fa-triangle-exclamation'); ?>"></i>
                        <span><?php echo htmlspecialchars($flash['message']); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- محتوى الـ View المُرسل من الـ Controller -->
            <?php echo $viewContent ?? ''; ?>

        </main>
    </div>

    <!-- سكربتات التحكم بالقالب -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuBtn = document.getElementById('mobileMenuBtn');

            function toggleMenu() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
            }

            if (menuBtn) menuBtn.addEventListener('click', toggleMenu);
            if (overlay) overlay.addEventListener('click', toggleMenu);

            // تفعيل الروابط النشطة في القائمة
            const currentPath = window.location.pathname.toLowerCase();
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href').toLowerCase();
                // مقارنة بسيطة لمعرفة ما إذا كان الرابط الحالي يطابق القائمة
                if (currentPath.includes(href.replace('<?php echo strtolower(URL_ROOT); ?>', ''))) {
                    // إزالة الـ Active من الجميع
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>