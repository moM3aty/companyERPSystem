<?php
// app/views/settings/index.php
 $settings = $data['settings'];
 $user = $data['user'];
 $sysInfo = $data['system_info'];
 $sysStats = $data['system_stats'];
 $flash = $data['flash'] ?? null;
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
            --primary: #14b8a6; --primary-dark: #0d9488; --primary-light: #ccfbf1;
            --accent: #f59e0b; --accent-light: #fef3c1;
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
        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }

        /* === رسائل Flash === */
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

        /* === تبويبات الإعدادات === */
        .settings-tabs {
            display: flex; gap: 4px;
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); padding: 6px;
            margin-bottom: 24px;
            overflow-x: auto;
            animation: fadeUp 0.5s ease both;
        }

        .tab-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: var(--radius-sm);
            border: none; background: transparent;
            font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600;
            color: var(--text-muted); cursor: pointer;
            transition: all 0.25s; white-space: nowrap;
        }

        .tab-btn:hover { color: var(--text-dark); background: var(--page-bg); }

        .tab-btn.active {
            background: var(--primary); color: #fff;
            box-shadow: 0 2px 8px rgba(20,184,166,0.25);
        }

        .tab-btn i { font-size: 14px; }

        /* محتوى التبويب */
        .tab-panel { display: none; animation: fadeIn 0.35s ease both; }
        .tab-panel.active { display: block; }

        .settings-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            overflow: hidden; margin-bottom: 20px;
        }

        .settings-card-header {
            padding: 20px 28px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }

        .settings-card-header .sch-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }

        .settings-card-header h3 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
        .settings-card-header p { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .settings-card-body { padding: 28px; }

        .settings-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .s-group { display: flex; flex-direction: column; }
        .s-group.full { grid-column: 1 / -1; }

        .s-label {
            font-size: 13px; font-weight: 600; color: var(--text-body);
            margin-bottom: 8px; display: flex; align-items: center; gap: 4px;
        }

        .s-label .req { color: var(--danger); }

        .s-input {
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 14px;
            background: var(--card-bg); color: var(--text-dark);
            outline: none; transition: all 0.25s;
        }

        .s-input::placeholder { color: var(--text-muted); }
        .s-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,184,166,0.08); }

        .s-hint { font-size: 11px; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 4px; }
        .s-hint i { font-size: 10px; }

        select.s-input {
            appearance: none; cursor: pointer; padding-left: 36px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: left 14px center;
        }

        .s-actions {
            padding: 20px 28px; background: #f8fafc;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }

        .btn-save {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 28px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 700;
            cursor: pointer; transition: all 0.25s;
            box-shadow: 0 2px 8px rgba(20,184,166,0.2);
        }

        .btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(20,184,166,0.3); }
        .btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* بطاقة الملف الشخصي */
        .profile-top {
            display: flex; align-items: center; gap: 24px;
            padding: 28px; border-bottom: 1px solid var(--border);
        }

        .profile-avatar {
            width: 88px; height: 88px; border-radius: 22px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: 800; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 6px 24px rgba(20,184,166,0.2);
        }

        .profile-meta h3 { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .profile-meta .pm-email { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
        .profile-meta .pm-role {
            display: inline-flex; align-items: center; gap: 5px;
            margin-top: 8px; padding: 4px 14px; border-radius: 8px;
            font-size: 11px; font-weight: 700;
            background: var(--primary-light); color: var(--primary-dark);
        }

        /* مؤشر قوة كلمة المرور */
        .password-strength {
            display: flex; gap: 4px; margin-top: 10px;
        }

        .ps-bar {
            flex: 1; height: 4px; border-radius: 2px;
            background: #e2e8f0; transition: background 0.3s;
        }

        .ps-label {
            font-size: 11px; font-weight: 600; margin-top: 6px;
            transition: color 0.3s;
        }

        /* معلومات النظام */
        .sys-info-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 1px; background: var(--border);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            overflow: hidden;
        }

        .sys-info-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 20px; background: var(--card-bg);
        }

        .sii-label { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; }
        .sii-label i { font-size: 12px; width: 16px; text-align: center; }
        .sii-value { font-size: 13px; font-weight: 700; color: var(--text-dark); direction: ltr; }

        /* إحصائيات النظام */
        .sys-stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 12px; margin-bottom: 20px;
        }

        .sys-stat-card {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 16px; text-align: center;
            transition: all 0.2s;
        }

        .sys-stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); }

        .sys-stat-card i { font-size: 20px; margin-bottom: 8px; }
        .sys-stat-card .ss-val { font-size: 22px; font-weight: 800; color: var(--text-dark); }
        .sys-stat-card .ss-lbl { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
            .settings-grid { grid-template-columns: 1fr; }
            .sys-info-grid { grid-template-columns: 1fr; }
            .sys-stats-grid { grid-template-columns: 1fr 1fr; }
            .profile-top { flex-direction: column; text-align: center; }
            .settings-tabs { gap: 2px; padding: 4px; }
            .tab-btn { padding: 8px 14px; font-size: 12px; }
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
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/sale" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/report" class="nav-link"><i class="fas fa-chart-line"></i><span>التقارير</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">النظام</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/settings" class="nav-link active"><i class="fas fa-gear"></i><span>الإعدادات</span></a></div>
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
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة" style="width:40px;height:40px;border-radius:10px;border:1px solid var(--border);background:transparent;color:var(--text-body);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:15px;"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $data['title']; ?></div>
                    <div class="breadcrumb"><a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a><i class="fas fa-chevron-left" style="font-size:9px;"></i><span>الإعدادات</span></div>
                </div>
            </div>
        </header>

        <div class="page-body">

            <!-- رسالة Flash -->
            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : ($flash['type'] === 'error' ? 'circle-xmark' : 'triangle-exclamation'); ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- التبويبات -->
            <div class="settings-tabs">
                <button class="tab-btn active" data-tab="company"><i class="fas fa-building"></i> الشركة</button>
                <button class="tab-btn" data-tab="profile"><i class="fas fa-user-gear"></i> الملف الشخصي</button>
                <button class="tab-btn" data-tab="security"><i class="fas fa-shield-halved"></i> الأمان</button>
                <button class="tab-btn" data-tab="system"><i class="fas fa-server"></i> النظام</button>
            </div>

            <!-- ===== تبويب: الشركة ===== -->
            <div class="tab-panel active" id="panel-company">
                <form action="<?php echo URL_ROOT; ?>/settings/index" method="POST">
                    <input type="hidden" name="form_action" value="save_company">

                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="sch-icon" style="background:var(--primary-light);color:var(--primary-dark);"><i class="fas fa-building"></i></div>
                            <div><h3>معلومات الشركة</h3><p>تظهر في الفواتير والتقارير المطبوعة</p></div>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-grid">
                                <div class="s-group">
                                    <label class="s-label">اسم الشركة <span class="req">*</span></label>
                                    <input type="text" name="company_name" class="s-input" value="<?php echo htmlspecialchars($settings['company_name']); ?>" required>
                                </div>
                                <div class="s-group">
                                    <label class="s-label">البريد الإلكتروني</label>
                                    <input type="email" name="company_email" class="s-input" value="<?php echo htmlspecialchars($settings['company_email']); ?>" style="direction:ltr;text-align:right;">
                                </div>
                                <div class="s-group">
                                    <label class="s-label">رقم الهاتف</label>
                                    <input type="text" name="company_phone" class="s-input" value="<?php echo htmlspecialchars($settings['company_phone']); ?>" style="direction:ltr;text-align:right;">
                                </div>
                                <div class="s-group">
                                    <label class="s-label">العملة الافتراضية <span class="req">*</span></label>
                                    <select name="currency" class="s-input">
                                        <option value="ر.س" <?php echo $settings['currency'] === 'ر.س' ? 'selected' : ''; ?>>ريال سعودي (ر.س)</option>
                                        <option value="ج.م" <?php echo $settings['currency'] === 'ج.م' ? 'selected' : ''; ?>>جنيه مصري (ج.م)</option>
                                        <option value="د.إ" <?php echo $settings['currency'] === 'د.إ' ? 'selected' : ''; ?>>درهم إماراتي (د.إ)</option>
                                        <option value="د.ك" <?php echo $settings['currency'] === 'د.ك' ? 'selected' : ''; ?>>دينار كويتي (د.ك)</option>
                                        <option value="$" <?php echo $settings['currency'] === '$' ? 'selected' : ''; ?>>دولار أمريكي ($)</option>
                                    </select>
                                </div>
                                <div class="s-group">
                                    <label class="s-label">نسبة الضريبة (%) <span class="req">*</span></label>
                                    <input type="number" name="tax_rate" class="s-input" value="<?php echo htmlspecialchars($settings['tax_rate']); ?>" min="0" max="100" step="0.1" style="direction:ltr;text-align:right;">
                                    <div class="s-hint"><i class="fas fa-info-circle"></i> تُطبق على الفواتير تلقائياً</div>
                                </div>
                            </div>
                        </div>
                        <div class="s-actions">
                            <button type="submit" class="btn-save" id="btnCompany"><i class="fas fa-save"></i> حفظ إعدادات الشركة</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ===== تبويب: الملف الشخصي ===== -->
            <div class="tab-panel" id="panel-profile">
                <div class="settings-card">
                    <div class="profile-top">
                        <div class="profile-avatar"><?php echo mb_substr($user->name ?? 'م', 0, 2); ?></div>
                        <div class="profile-meta">
                            <h3><?php echo htmlspecialchars($user->name ?? ''); ?></h3>
                            <div class="pm-email"><i class="fas fa-envelope" style="margin-left:4px;font-size:11px;"></i> <?php echo htmlspecialchars($user->email ?? ''); ?></div>
                            <div class="pm-role"><i class="fas fa-shield-halved"></i> <?php echo htmlspecialchars($user->role ?? 'مدير النظام'); ?></div>
                        </div>
                    </div>
                    <form action="<?php echo URL_ROOT; ?>/settings/index" method="POST">
                        <input type="hidden" name="form_action" value="save_profile">
                        <div class="settings-card-body">
                            <div class="settings-grid">
                                <div class="s-group">
                                    <label class="s-label">الاسم الكامل <span class="req">*</span></label>
                                    <input type="text" name="profile_name" class="s-input" value="<?php echo htmlspecialchars($user->name ?? ''); ?>" required>
                                </div>
                                <div class="s-group">
                                    <label class="s-label">البريد الإلكتروني <span class="req">*</span></label>
                                    <input type="email" name="profile_email" class="s-input" value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required style="direction:ltr;text-align:right;">
                                </div>
                                <div class="s-group">
                                    <label class="s-label">رقم الهاتف</label>
                                    <input type="text" name="profile_phone" class="s-input" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>" placeholder="05xxxxxxxx" style="direction:ltr;text-align:right;">
                                </div>
                                <div class="s-group">
                                    <label class="s-label">الصلاحية</label>
                                    <input type="text" class="s-input" value="<?php echo htmlspecialchars($user->role ?? 'مدير النظام'); ?>" disabled style="background:#f8fafc;color:var(--text-muted);cursor:default;">
                                    <div class="s-hint"><i class="fas fa-lock"></i> لا يمكن تغيير الصلاحية من هنا</div>
                                </div>
                            </div>
                        </div>
                        <div class="s-actions">
                            <button type="submit" class="btn-save" id="btnProfile"><i class="fas fa-save"></i> تحديث الملف الشخصي</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===== تبويب: الأمان ===== -->
            <div class="tab-panel" id="panel-security">
                <form action="<?php echo URL_ROOT; ?>/settings/index" method="POST">
                    <input type="hidden" name="form_action" value="change_password">

                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="sch-icon" style="background:var(--danger-light);color:var(--danger);"><i class="fas fa-key"></i></div>
                            <div><h3>تغيير كلمة المرور</h3><p>ننصح بتغيير كلمة المرور بشكل دوري</p></div>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-grid">
                                <div class="s-group full">
                                    <label class="s-label">كلمة المرور الحالية <span class="req">*</span></label>
                                    <input type="password" name="current_password" class="s-input" id="currentPass" required placeholder="أدخل كلمة المرور الحالية">
                                </div>
                                <div class="s-group">
                                    <label class="s-label">كلمة المرور الجديدة <span class="req">*</span></label>
                                    <input type="password" name="new_password" class="s-input" id="newPass" required placeholder="6 أحرف على الأقل" oninput="checkStrength(this.value)">
                                    <div class="password-strength" id="strengthBars">
                                        <div class="ps-bar" id="ps1"></div>
                                        <div class="ps-bar" id="ps2"></div>
                                        <div class="ps-bar" id="ps3"></div>
                                        <div class="ps-bar" id="ps4"></div>
                                    </div>
                                    <div class="ps-label" id="strengthLabel"></div>
                                </div>
                                <div class="s-group">
                                    <label class="s-label">تأكيد كلمة المرور الجديدة <span class="req">*</span></label>
                                    <input type="password" name="confirm_password" class="s-input" id="confirmPass" required placeholder="أعد كتابة كلمة المرور" oninput="checkMatch()">
                                    <div class="ps-label" id="matchLabel"></div>
                                </div>
                            </div>
                        </div>
                        <div class="s-actions">
                            <button type="submit" class="btn-save" id="btnPass" style="background:linear-gradient(135deg, var(--danger), #dc2626);box-shadow:0 2px 8px rgba(239,68,68,0.2);"><i class="fas fa-key"></i> تغيير كلمة المرور</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ===== تبويب: النظام ===== -->
            <div class="tab-panel" id="panel-system">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <div class="sch-icon" style="background:var(--info-light);color:var(--info);"><i class="fas fa-server"></i></div>
                        <div><h3>معلومات النظام</h3><p>بيانات تقنية للنظام والخادم</p></div>
                    </div>
                    <div class="settings-card-body" style="padding:0;">
                        <div class="sys-info-grid">
                            <div class="sys-info-item"><span class="sii-label"><i class="fas fa-code-branch"></i> إصدار النظام</span><span class="sii-value">v<?php echo $sysInfo['app_version']; ?></span></div>
                            <div class="sys-info-item"><span class="sii-label"><i class="fab fa-php"></i> إصدار PHP</span><span class="sii-value"><?php echo $sysInfo['php_version']; ?></span></div>
                            <div class="sys-info-item"><span class="sii-label"><i class="fas fa-globe"></i> الخادم</span><span class="sii-value" style="font-size:11px;"><?php echo htmlspecialchars(substr($sysInfo['server_software'], 0, 40)); ?></span></div>
                            <div class="sys-info-item"><span class="sii-label"><i class="fas fa-database"></i> قاعدة البيانات</span><span class="sii-value"><?php echo $sysInfo['db_name']; ?></span></div>
                            <div class="sys-info-item"><span class="sii-label"><i class="fas fa-clock"></i> المنطقة الزمنية</span><span class="sii-value"><?php echo $sysInfo['timezone']; ?></span></div>
                            <div class="sys-info-item"><span class="sii-label"><i class="fas fa-upload"></i> الحد الأقصى للرفع</span><span class="sii-value"><?php echo $sysInfo['max_upload']; ?></span></div>
                            <div class="sys-info-item"><span class="sii-label"><i class="fas fa-memory"></i> حد الذاكرة</span><span class="sii-value"><?php echo $sysInfo['memory_limit']; ?></span></div>
                            <div class="sys-info-item"><span class="sii-label"><i class="fas fa-plug"></i> مضيف DB</span><span class="sii-value"><?php echo $sysInfo['db_host']; ?></span></div>
                        </div>
                    </div>
                </div>

                <div style="margin-top:20px;">
                    <h3 style="font-size:15px;font-weight:700;color:var(--text-dark);margin-bottom:14px;display:flex;align-items:center;gap:8px;"><i class="fas fa-database" style="color:var(--purple);"></i> إحصائيات قاعدة البيانات</h3>
                    <div class="sys-stats-grid">
                        <div class="sys-stat-card"><i class="fas fa-users" style="color:var(--info);"></i><div class="ss-val"><?php echo $sysStats['employees']; ?></div><div class="ss-lbl">موظف</div></div>
                        <div class="sys-stat-card"><i class="fas fa-box" style="color:var(--accent);"></i><div class="ss-val"><?php echo $sysStats['products']; ?></div><div class="ss-lbl">منتج</div></div>
                        <div class="sys-stat-card"><i class="fas fa-receipt" style="color:var(--success);"></i><div class="ss-val"><?php echo $sysStats['invoices']; ?></div><div class="ss-lbl">فاتورة</div></div>
                        <div class="sys-stat-card"><i class="fas fa-money-bill" style="color:var(--danger);"></i><div class="ss-val"><?php echo $sysStats['expenses']; ?></div><div class="ss-lbl">مصروف</div></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        /* === التبويبات === */
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('panel-' + this.dataset.tab).classList.add('active');
            });
        });

        /* === مؤشر قوة كلمة المرور === */
        function checkStrength(pass) {
            let score = 0;
            if (pass.length >= 6) score++;
            if (pass.length >= 10) score++;
            if (/[A-Z]/.test(pass) && /[a-z]/.test(pass)) score++;
            if (/[0-9]/.test(pass) && /[^A-Za-z0-9]/.test(pass)) score++;

            const colors = ['', '#ef4444', '#f59e0b', '#06b6d4', '#22c55e'];
            const labels = ['', 'ضعيفة جداً', 'ضعيفة', 'متوسطة', 'قوية'];

            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('ps' + i);
                bar.style.background = i <= score ? colors[score] : '#e2e8f0';
            }

            const label = document.getElementById('strengthLabel');
            label.textContent = pass.length > 0 ? labels[score] : '';
            label.style.color = colors[score] || 'var(--text-muted)';
        }

        /* === تطابق كلمة المرور === */
        function checkMatch() {
            const np = document.getElementById('newPass').value;
            const cp = document.getElementById('confirmPass').value;
            const label = document.getElementById('matchLabel');

            if (cp.length === 0) {
                label.textContent = '';
            } else if (np === cp) {
                label.textContent = '✓ متطابقتان';
                label.style.color = 'var(--success)';
            } else {
                label.textContent = '✗ غير متطابقتان';
                label.style.color = 'var(--danger)';
            }
        }

        /* === حالة التحميل للأزرار === */
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('.btn-save');
                if (btn) {
                    btn.disabled = true;
                    const origHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الحفظ...';
                    setTimeout(() => { btn.innerHTML = origHTML; btn.disabled = false; }, 3000);
                }
            });
        });

        /* === موبايل === */
        const sidebar = document.getElementById('sidebar'); const overlay = document.getElementById('sidebarOverlay'); const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>