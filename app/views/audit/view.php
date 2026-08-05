<?php
// app/views/audit/view.php
$pageTitle = $data['title'] ?? 'تفاصيل السجل';
$log = $data['log'] ?? null;
$flash = $data['flash'] ?? null;
$currentUrl = 'audit/index'; // نُبقي القائمة الجانبية مؤشرة على السجل
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

        /* القائمة الجانبية (Sidebar) */
        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%); z-index: 100; display: flex; flex-direction: column; transition: transform 0.3s ease; border-left: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { padding: 24px 24px 20px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar-brand .s-logo { width: 42px; height: 42px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; flex-shrink: 0; }
        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: var(--radius-sm); color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s ease; position: relative; }
        .nav-link i { width: 20px; text-align: center; font-size: 15px; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }
        .nav-link.active { background: rgba(20, 184, 166, 0.1); color: var(--primary); font-weight: 600; }
        .nav-link.active::before { content: ''; position: absolute; right: -12px; top: 50%; transform: translateY(-50%); width: 3px; height: 24px; background: var(--primary); border-radius: 0 4px 4px 0; }
        .sidebar-user { padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; align-items: center; gap: 12px; }
        .sidebar-user .su-avatar { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .sidebar-user .su-info { flex: 1; min-width: 0; }
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto; }
        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239, 68, 68, 0.1); }

        /* المحتوى الرئيسي */
        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; transition: margin 0.3s ease; }
        .topbar { height: var(--topbar-h); background: var(--card-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .page-title { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .breadcrumb a:hover { color: var(--primary); }
        .mobile-menu-btn { display: none; }
        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        
        .flash-msg { padding: 14px 20px; border-radius: var(--radius-sm); display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; margin-bottom: 24px; animation: fadeUp 0.4s ease both; border: 1px solid transparent; }
        .flash-msg.flash-warning { background: var(--accent-light); color: #b45309; border-color: #fde68a; }

        /* تصميم كرت التفاصيل */
        .detail-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease both; max-width: 900px; }
        .dc-header { padding: 24px 32px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: #f8fafc;}
        .dc-title { display: flex; align-items: center; gap: 12px; font-size: 16px; font-weight: 700; color: var(--text-dark); }
        .dc-title i { color: var(--primary); font-size: 20px; }
        
        .btn-back { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #fff; color: var(--text-body); border: 1px solid var(--border); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .btn-back:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }

        .dc-body { padding: 32px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        
        .info-group { display: flex; flex-direction: column; gap: 6px; }
        .info-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 14px; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 8px;}
        .info-value.monospace { font-family: monospace; font-size: 13px; color: var(--primary-dark); background: var(--primary-light); padding: 4px 10px; border-radius: 6px; display: inline-block; width: fit-content;}

        .json-section { grid-column: 1 / -1; margin-top: 10px; }
        .json-card { background: #1e293b; border-radius: var(--radius-sm); padding: 16px; overflow-x: auto; margin-top: 8px; border: 1px solid #334155;}
        .json-card pre { font-family: monospace; font-size: 13px; color: #e2e8f0; line-height: 1.6; margin: 0; direction: ltr; text-align: left; }
        .json-key { color: #38bdf8; }
        .json-string { color: #a3e635; }
        .json-number { color: #f472b6; }
        .json-null { color: #94a3b8; font-style: italic; }

        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;}
        .badge-insert { background: var(--success-light); color: #15803d; border: 1px solid #bbf7d0;}
        .badge-update { background: var(--accent-light); color: #b45309; border: 1px solid #fde68a;}
        .badge-delete { background: var(--danger-light); color: #dc2626; border: 1px solid #fecaca;}
        .badge-login  { background: var(--info-light); color: #0e7490; border: 1px solid #a5f3fc;}
        .badge-default{ background: var(--border); color: var(--text-body); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer; }
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .dc-body { grid-template-columns: 1fr; }
            .dc-header { flex-direction: column; align-items: flex-start; gap: 16px;}
            .btn-back { width: 100%; justify-content: center;}
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; backdrop-filter: blur(2px); }
        .sidebar-overlay.show { display: block; }

    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text"><span class="s-name">ERP <span>Pro</span></span></div>
        </div>
        <?php if(class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
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
                        <a href="<?php echo URL_ROOT; ?>/audit/index">سجل التدقيق</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>تفاصيل السجل #<?php echo $log->id ?? ''; ?></span>
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

            <?php if ($log): 
                $actionClass = match(strtolower($log->action)) {
                    'insert', 'create', 'add' => 'badge-insert',
                    'update', 'edit', 'modify' => 'badge-update',
                    'delete', 'remove' => 'badge-delete',
                    'login', 'auth' => 'badge-login',
                    default => 'badge-default'
                };
                
                // دالة بسيطة لتلوين الـ JSON
                function prettyJson($array) {
                    if (!$array) return '<span class="json-null">null</span>';
                    $json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    $json = preg_replace('/"([^"]+)"\s*:/', '<span class="json-key">"$1"</span>:', $json);
                    $json = preg_replace('/:\s*"([^"]*)"/', ': <span class="json-string">"$1"</span>', $json);
                    $json = preg_replace('/:\s*(-?\d+(?:\.\d+)?)/', ': <span class="json-number">$1</span>', $json);
                    $json = preg_replace('/:\s*(null)/i', ': <span class="json-null">$1</span>', $json);
                    return $json;
                }
            ?>
            <div class="detail-card">
                <div class="dc-header">
                    <div class="dc-title">
                        <i class="fas fa-file-lines"></i> معلومات النشاط
                    </div>
                    <a href="<?php echo URL_ROOT; ?>/audit/index" class="btn-back"><i class="fas fa-arrow-right"></i> العودة للسجل</a>
                </div>
                <div class="dc-body">
                    
                    <div class="info-group">
                        <div class="info-label">المستخدم</div>
                        <div class="info-value"><i class="fas fa-user" style="color:var(--text-muted);"></i> <?php echo htmlspecialchars($log->user_name ?? 'غير معروف'); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">الإجراء (Action)</div>
                        <div class="info-value"><span class="badge <?php echo $actionClass; ?>"><?php echo htmlspecialchars($log->action); ?></span></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">الجدول المستهدف (Table)</div>
                        <div class="info-value monospace"><?php echo htmlspecialchars($log->table_name); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">معرف السجل (Record ID)</div>
                        <div class="info-value"><?php echo $log->record_id ? '#' . $log->record_id : '—'; ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">عنوان IP</div>
                        <div class="info-value"><i class="fas fa-network-wired" style="color:var(--text-muted);"></i> <?php echo htmlspecialchars($log->ip_address ?? '—'); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">تاريخ ووقت النشاط</div>
                        <div class="info-value"><i class="far fa-clock" style="color:var(--text-muted);"></i> <span style="direction:ltr;"><?php echo date('Y-m-d H:i:s', strtotime($log->created_at)); ?></span></div>
                    </div>

                    <div class="info-group" style="grid-column: 1 / -1;">
                        <div class="info-label">المتصفح والجهاز (User Agent)</div>
                        <div class="info-value" style="font-size:12px; color:var(--text-muted); background:var(--page-bg); padding:10px 14px; border-radius:8px; border:1px solid var(--border);">
                            <?php echo htmlspecialchars($log->user_agent ?? '—'); ?>
                        </div>
                    </div>

                    <div class="json-section">
                        <div class="info-label"><i class="fas fa-database" style="color:var(--text-muted);"></i> البيانات القديمة (Before)</div>
                        <div class="json-card">
                            <pre><?php echo prettyJson($log->old_data); ?></pre>
                        </div>
                    </div>

                    <div class="json-section">
                        <div class="info-label"><i class="fas fa-database" style="color:var(--primary);"></i> البيانات الجديدة (After)</div>
                        <div class="json-card">
                            <pre><?php echo prettyJson($log->new_data); ?></pre>
                        </div>
                    </div>

                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>