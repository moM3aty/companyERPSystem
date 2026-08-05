<?php
// app/views/purchase/receive.php
$pageTitle = $data['title'] ?? 'استلام بضاعة';
$order = $data['order'] ?? null;
$items = $data['items'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'purchase/index';
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
        .flash-msg.flash-error { background: var(--danger-light); color: #dc2626; border-color: #fecaca; }

        .header-info-card { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: var(--radius); padding: 28px 32px; color: #fff; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; animation: fadeUp 0.5s ease both; }
        .hic-left { display: flex; align-items: center; gap: 16px; }
        .hic-icon { width: 56px; height: 56px; border-radius: 14px; background: rgba(20,184,166,0.15); color: var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .hic-title { font-size: 20px; font-weight: 800; margin-bottom: 4px; }
        .hic-subtitle { font-size: 13px; color: #94a3b8; display: flex; align-items: center; gap: 6px; }
        .hic-right { text-align: left; }
        .hic-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .hic-val { font-size: 24px; font-weight: 800; font-variant-numeric: tabular-nums; direction: ltr; }

        .form-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.1s both; }
        
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 14px 20px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; background: #f8fafc; border-bottom: 1.5px solid var(--border); text-align: right; white-space: nowrap; }
        thead th.center { text-align: center; }
        tbody tr { transition: background 0.15s; border-bottom: 1px solid var(--border); }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(20, 184, 166, 0.02); }
        tbody td { padding: 14px 20px; font-size: 13.5px; color: var(--text-body); vertical-align: middle;}
        tbody td.center { text-align: center; }
        
        .prod-name { font-weight: 600; color: var(--text-dark); display:block; margin-bottom: 2px;}
        .prod-meta { font-size: 11px; color: var(--text-muted); }
        
        .qty-badge { display: inline-flex; justify-content: center; align-items: center; width: 36px; height: 36px; border-radius: 8px; font-weight: 700; background: var(--page-bg); color: var(--text-dark); border: 1px solid var(--border);}
        
        .form-input { width: 100px; padding: 10px 12px; border: 1.5px solid var(--border); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 14px; background: var(--card-bg); color: var(--text-dark); outline: none; transition: all 0.2s; text-align: center; direction: ltr; font-weight: 700;}
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,184,166,0.08); }
        .form-input:read-only { background: var(--success-light); color: var(--success); border-color: transparent; }

        .status-tag { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; background: var(--success-light); color: #15803d; }

        .form-actions { padding: 24px 32px; display: flex; align-items: center; gap: 12px; background: #f8fafc; border-top: 1px solid var(--border); }
        .btn-submit { display: inline-flex; align-items: center; gap: 8px; padding: 12px 32px; background: linear-gradient(135deg, var(--success), #16a34a); color: #fff; border: none; border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s; box-shadow: 0 4px 15px rgba(34,197,94,0.25); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34,197,94,0.35); }
        .btn-cancel { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: transparent; color: var(--text-body); border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-cancel:hover { background: var(--page-bg); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; } .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer;}
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .header-info-card { flex-direction: column; align-items: flex-start; text-align: right; }
            .hic-right { text-align: right; width: 100%; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;}
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
                        <a href="<?php echo URL_ROOT; ?>/purchase/index">أوامر الشراء</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>استلام بضاعة</span>
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

            <div class="header-info-card">
                <div class="hic-left">
                    <div class="hic-icon"><i class="fas fa-box-open"></i></div>
                    <div>
                        <div class="hic-title">أمر شراء: <?php echo htmlspecialchars($order->po_number); ?></div>
                        <div class="hic-subtitle">
                            <i class="fas fa-truck-field"></i> المورد: <strong><?php echo htmlspecialchars($order->supplier_name); ?></strong>
                            &nbsp;|&nbsp; <i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($order->created_at)); ?>
                        </div>
                    </div>
                </div>
                <div class="hic-right">
                    <div class="hic-label">قيمة الطلب الإجمالية</div>
                    <div class="hic-val"><?php echo number_format($order->total_amount, 2); ?> <span style="font-size:14px;">ر.س</span></div>
                </div>
            </div>

            <div class="form-card">
                <form action="<?php echo URL_ROOT; ?>/purchase/receive/<?php echo $order->id; ?>" method="POST" id="receiveForm">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المنتج</th>
                                    <th class="center">الكمية المطلوبة</th>
                                    <th class="center">مستلم سابقاً</th>
                                    <th class="center" style="color:var(--primary-dark);">الكمية المستلمة الآن</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $i = 1; 
                                    $allReceived = true;
                                    foreach ($items as $item) : 
                                        $remaining = $item->quantity_ordered - $item->quantity_received;
                                        if($remaining > 0) $allReceived = false;
                                ?>
                                <tr>
                                    <td style="color:var(--text-muted); font-size:12px; font-weight:600;"><?php echo $i++; ?></td>
                                    <td>
                                        <span class="prod-name"><?php echo htmlspecialchars($item->product_name ?? 'منتج #' . $item->product_id); ?></span>
                                        <span class="prod-meta">الكمية المتبقية للاستلام: <strong><?php echo $remaining; ?></strong></span>
                                    </td>
                                    <td class="center"><div class="qty-badge"><?php echo $item->quantity_ordered; ?></div></td>
                                    <td class="center">
                                        <div class="qty-badge" style="<?php echo $item->quantity_received > 0 ? 'background:var(--success-light);color:var(--success);border-color:transparent;' : ''; ?>">
                                            <?php echo $item->quantity_received; ?>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <?php if($remaining > 0): ?>
                                            <input type="number" name="received_items[<?php echo $item->product_id; ?>][quantity_received]" 
                                                   class="form-input rec-input" min="0" max="<?php echo $remaining; ?>" 
                                                   value="<?php echo $remaining; ?>" required>
                                            <input type="hidden" name="received_items[<?php echo $item->product_id; ?>][product_id]" value="<?php echo $item->product_id; ?>">
                                        <?php else: ?>
                                            <span class="status-tag"><i class="fas fa-check"></i> مكتمل</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="form-actions">
                        <?php if(!$allReceived): ?>
                            <button type="submit" class="btn-submit" id="btnSubmit">
                                <i class="fas fa-check-double"></i> تأكيد إدخال الكميات للمخزون
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn-submit" style="background:var(--text-muted);box-shadow:none;cursor:not-allowed;" disabled>
                                <i class="fas fa-check-circle"></i> تم استلام كافة الكميات
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo URL_ROOT; ?>/purchase/index" class="btn-cancel">رجوع للقائمة</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const form = document.getElementById('receiveForm');
        const inputs = document.querySelectorAll('.rec-input');
        const btnSubmit = document.getElementById('btnSubmit');

        if(form && btnSubmit) {
            form.addEventListener('submit', function(e) {
                let hasValue = false;
                inputs.forEach(input => {
                    if (parseInt(input.value) > 0) hasValue = true;
                });

                if(!hasValue) {
                    e.preventDefault();
                    alert('يجب إدخال كمية لمنتج واحد على الأقل ليتم الاستلام.');
                    return;
                }

                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري تحديث المخزون...';
            });
        }

        // القائمة الجانبية للموبايل
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>