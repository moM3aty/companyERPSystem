<?php
// app/views/helpdesk/show.php
$pageTitle = $data['title'] ?? 'تفاصيل التذكرة';
$ticket = $data['ticket'] ?? null;
$flash = $data['flash'] ?? null;
$currentUrl = 'helpdesk/index';

// جلب الموظفين لتعيين التذكرة لهم
$db = Database::getInstance();
$db->query("SELECT id, name FROM users ORDER BY name ASC");
$users = $db->resultSet();

$stClass = match($ticket->status) {
    'open' => 'st-open', 'in_progress' => 'st-in_progress', 'resolved' => 'st-resolved', 'closed' => 'st-closed', default => 'st-open'
};
$stLabel = match($ticket->status) {
    'open' => 'مفتوحة', 'in_progress' => 'قيد المعالجة', 'resolved' => 'تم الحل', 'closed' => 'مغلقة', default => $ticket->status
};
$prClass = match($ticket->priority) {
    'low' => 'pr-low', 'medium' => 'pr-medium', 'high' => 'pr-high', 'urgent' => 'pr-urgent', default => 'pr-medium'
};
$prLabel = match($ticket->priority) {
    'low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'حرجة جداً', default => $ticket->priority
};
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* القائمة الجانبية */
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
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto;}
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
        .flash-msg.flash-success { background: var(--success-light); color: #15803d; border-color: #bbf7d0; }
        .flash-msg.flash-error { background: var(--danger-light); color: #dc2626; border-color: #fecaca; }

        /* تصميم عرض التذكرة */
        .ticket-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; animation: fadeUp 0.5s ease both; align-items: start;}
        
        .ticket-main { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; }
        .ticket-header { padding: 28px 32px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, #f8fafc, #ffffff); }
        .th-meta { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .tkt-num { font-family: monospace; font-size: 14px; font-weight: 700; color: var(--info-dark); background: var(--info-light); padding: 4px 12px; border-radius: 6px; direction: ltr; display: inline-block; }
        
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; }
        .st-open { background: var(--danger-light); color: var(--danger); }
        .st-in_progress { background: var(--accent-light); color: var(--accent); }
        .st-resolved { background: var(--success-light); color: var(--success); }
        .st-closed { background: var(--border); color: var(--text-body); }
        
        .pr-low { color: var(--success); }
        .pr-medium { color: var(--info); }
        .pr-high { color: var(--accent); }
        .pr-urgent { color: var(--danger); font-weight: 800; }

        .ticket-header h2 { font-size: 20px; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; line-height: 1.4; }
        .th-client { font-size: 13px; color: var(--text-body); display: flex; align-items: center; gap: 8px; }
        .th-client i { color: var(--text-muted); }

        .ticket-body { padding: 32px; font-size: 14px; color: var(--text-body); line-height: 1.8; white-space: pre-wrap; }
        .tb-label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px; display: block; }

        /* الشريط الجانبي للتحديث */
        .ticket-sidebar { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; }
        .ts-header { padding: 20px 24px; border-bottom: 1px solid var(--border); font-size: 15px; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        .ts-header i { color: var(--primary); }
        
        .ts-body { padding: 24px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 20px; }
        .form-label { font-size: 13px; font-weight: 600; color: var(--text-body); margin-bottom: 8px; }
        .form-input { padding: 12px 16px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; background: var(--card-bg); color: var(--text-dark); outline: none; transition: all 0.25s; cursor: pointer; }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,184,166,0.08); }
        select.form-input { appearance: none; padding-left: 36px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: left 14px center; }
        
        .btn-submit { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; width: 100%; background: linear-gradient(135deg, var(--info), #0891b2); color: #fff; border: none; border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s; box-shadow: 0 4px 15px rgba(6,182,212,0.25); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(6,182,212,0.35); }

        .ts-info { margin-top: 24px; padding-top: 20px; border-top: 1px dashed var(--border); }
        .tsi-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 12px; }
        .tsi-label { color: var(--text-muted); font-weight: 600; }
        .tsi-val { color: var(--text-dark); font-weight: 700; }

        @media (max-width: 992px) {
            .ticket-layout { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer;}
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
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
                        <a href="<?php echo URL_ROOT; ?>/helpdesk/index">خدمة العملاء</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>معالجة تذكرة</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-body">
            
            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-circle-xmark"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="ticket-layout">
                
                <div class="ticket-main">
                    <div class="ticket-header">
                        <div class="th-meta">
                            <span class="tkt-num"><?php echo htmlspecialchars($ticket->ticket_number); ?></span>
                            <span class="badge <?php echo $stClass; ?>"><?php echo $stLabel; ?></span>
                        </div>
                        <h2><?php echo htmlspecialchars($ticket->subject); ?></h2>
                        <div class="th-client">
                            <i class="fas fa-user-circle"></i> العميل: <strong><?php echo htmlspecialchars($ticket->customer_name ?? 'غير محدد'); ?></strong>
                        </div>
                    </div>
                    <div class="ticket-body">
                        <span class="tb-label"><i class="fas fa-align-right"></i> وصف المشكلة / الطلب:</span>
                        <?php echo nl2br(htmlspecialchars($ticket->description)); ?>
                    </div>
                </div>

                <div class="ticket-sidebar">
                    <div class="ts-header"><i class="fas fa-sliders"></i> معالجة وتحديث الحالة</div>
                    <div class="ts-body">
                        <form action="<?php echo URL_ROOT; ?>/helpdesk/updateStatus/<?php echo $ticket->id; ?>" method="POST">
                            
                            <div class="form-group">
                                <label class="form-label">تحديث الحالة</label>
                                <select name="status" class="form-input">
                                    <option value="open" <?php echo $ticket->status === 'open' ? 'selected' : ''; ?>>مفتوحة</option>
                                    <option value="in_progress" <?php echo $ticket->status === 'in_progress' ? 'selected' : ''; ?>>قيد المعالجة</option>
                                    <option value="resolved" <?php echo $ticket->status === 'resolved' ? 'selected' : ''; ?>>تم الحل</option>
                                    <option value="closed" <?php echo $ticket->status === 'closed' ? 'selected' : ''; ?>>مغلقة</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">تعيين إلى (موظف الدعم)</label>
                                <select name="assigned_to" class="form-input">
                                    <option value="">-- غير مُعين --</option>
                                    <?php foreach($users as $u) : ?>
                                        <option value="<?php echo $u->id; ?>" <?php echo $ticket->assigned_to == $u->id ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($u->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ التحديثات</button>

                        </form>

                        <div class="ts-info">
                            <div class="tsi-row">
                                <span class="tsi-label">الأولوية</span>
                                <span class="tsi-val <?php echo $prClass; ?>"><?php echo $prLabel; ?></span>
                            </div>
                            <div class="tsi-row">
                                <span class="tsi-label">تاريخ الفتح</span>
                                <span class="tsi-val" style="direction:ltr;"><?php echo date('Y-m-d H:i', strtotime($ticket->created_at)); ?></span>
                            </div>
                            <div class="tsi-row">
                                <span class="tsi-label">آخر تحديث</span>
                                <span class="tsi-val" style="direction:ltr;"><?php echo date('Y-m-d H:i', strtotime($ticket->updated_at)); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        // القائمة الجانبية للموبايل
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>