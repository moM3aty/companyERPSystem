<?php
// app/views/attendance/index.php
$pageTitle = $data['title'] ?? 'الحضور والانصراف';
$records = $data['records'] ?? [];
$currentDate = $data['current_date'] ?? date('Y-m-d');
$flash = $data['flash'] ?? null;
$currentUrl = 'attendance/index';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | ERP Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; overflow-x: hidden; }

        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%); z-index: 100; display: flex; flex-direction: column; transition: transform 0.3s ease; border-left: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { padding: 24px 24px 20px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar-brand .s-logo { width: 42px; height: 42px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; flex-shrink: 0; box-shadow: 0 4px 15px rgba(20,184,166,0.25); }
        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; letter-spacing: -0.3px; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: var(--radius-sm); color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s ease; position: relative; }
        .nav-link i { width: 20px; text-align: center; font-size: 15px; transition: color 0.2s; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }
        .nav-link.active { background: rgba(20, 184, 166, 0.1); color: var(--primary); font-weight: 600; }
        .nav-link.active::before { content: ''; position: absolute; right: -12px; top: 50%; transform: translateY(-50%); width: 3px; height: 24px; background: var(--primary); border-radius: 0 4px 4px 0; }
        .sidebar-user { padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; align-items: center; gap: 12px; }
        .sidebar-user .su-avatar { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .sidebar-user .su-info { flex: 1; min-width: 0; }
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto;}
        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239, 68, 68, 0.1); }

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
        .flash-msg.flash-success { background: var(--success-light); color: #15803d; border-color: #bbf7d0; }
        .flash-msg.flash-error { background: var(--danger-light); color: #dc2626; border-color: #fecaca; }

        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; animation: fadeUp 0.5s ease both; }
        .toolbar-right h3 { font-size: 18px; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 8px; margin:0;}
        .toolbar-right h3 i { color: var(--info); }
        
        .date-filter { display: flex; align-items: center; gap: 10px; background: var(--card-bg); padding: 6px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); }
        .date-filter input { border: none; outline: none; font-family: 'Cairo'; font-size: 14px; font-weight: 600; color: var(--text-dark); }
        .date-filter button { background: var(--page-bg); border: 1px solid var(--border); padding: 4px 10px; border-radius: 6px; cursor: pointer; color: var(--text-body); font-family: 'Cairo'; font-size: 12px; font-weight: 600; }
        .date-filter button:hover { background: var(--border); }

        .btn-add { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: linear-gradient(135deg, var(--info), #0891b2); color: #fff; border: none; border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.25s; box-shadow: 0 2px 10px rgba(6,182,212,0.2); }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(6,182,212,0.3); }

        .table-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.15s both; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 14px 20px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; background: #f8fafc; border-bottom: 1.5px solid var(--border); text-align: right; white-space: nowrap; }
        tbody tr { transition: background 0.15s; border-bottom: 1px solid var(--border); }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(6,182,212,0.02); }
        tbody td { padding: 14px 20px; font-size: 13.5px; color: var(--text-body); vertical-align: middle;}
        
        .emp-name { font-weight: 700; color: var(--text-dark); display: block; }
        .emp-pos { font-size: 11px; color: var(--text-muted); }
        .time-badge { font-family: monospace; font-size: 13px; background: var(--page-bg); padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border); font-weight: 600; direction: ltr; display: inline-block; }

        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; }
        .badge-present { background: var(--success-light); color: #15803d; }
        .badge-absent { background: var(--danger-light); color: #dc2626; }
        .badge-late { background: var(--accent-light); color: #b45309; }
        .badge-leave { background: var(--purple-light); color: var(--purple); }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 48px; color: var(--border); margin-bottom: 16px; display: block; }
        .empty-state h4 { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .empty-state p { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer; }
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .toolbar { flex-direction: column; align-items: stretch; gap: 12px;}
            .toolbar-right { justify-content: space-between; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; backdrop-filter: blur(2px);}
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
                <div class="su-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'مدير النظام'); ?></div>
                <div class="su-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'admin'); ?></div>
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
                        <span>الموارد البشرية</span>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>الحضور والانصراف</span>
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
                    <h3><i class="fas fa-fingerprint"></i> السجل اليومي للحضور</h3>
                </div>
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <form action="<?php echo URL_ROOT; ?>/attendance/index" method="GET" class="date-filter">
                        <input type="date" name="date" value="<?php echo htmlspecialchars($currentDate); ?>">
                        <button type="submit">عرض اليوم</button>
                    </form>
                    <a href="<?php echo URL_ROOT; ?>/attendance/create" class="btn-add">
                        <i class="fas fa-user-clock"></i> تسجيل حالة الموظف
                    </a>
                </div>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>وقت الحضور</th>
                                <th>وقت الانصراف</th>
                                <th style="text-align:center;">الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $rec) : 
                                $statusClass = match($rec->status) {
                                    'present' => 'badge-present',
                                    'absent' => 'badge-absent',
                                    'late' => 'badge-late',
                                    'leave' => 'badge-leave',
                                    default => 'badge-absent'
                                };
                                $statusLabel = match($rec->status) {
                                    'present' => 'حاضر',
                                    'absent' => 'غائب',
                                    'late' => 'متأخر',
                                    'leave' => 'مجاز',
                                    default => $rec->status
                                };
                            ?>
                            <tr>
                                <td>
                                    <span class="emp-name"><?php echo htmlspecialchars($rec->employee_name); ?></span>
                                    <span class="emp-pos"><?php echo htmlspecialchars($rec->position ?? 'موظف'); ?></span>
                                </td>
                                <td>
                                    <?php if ($rec->check_in) : ?>
                                        <span class="time-badge"><i class="fas fa-sign-in-alt" style="color:var(--success); margin-right:4px;"></i> <?php echo date('H:i', strtotime($rec->check_in)); ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted); font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($rec->check_out) : ?>
                                        <span class="time-badge"><i class="fas fa-sign-out-alt" style="color:var(--danger); margin-right:4px;"></i> <?php echo date('H:i', strtotime($rec->check_out)); ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted); font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:12px;color:var(--text-muted);"><?php echo htmlspecialchars($rec->notes ?? '—'); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($records)) : ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-fingerprint"></i>
                                        <h4>لا يوجد سجلات حضور لهذا اليوم</h4>
                                        <p>لم يتم إدخال أو تسجيل بصمات الموظفين لتاريخ <?php echo htmlspecialchars($currentDate); ?></p>
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

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>