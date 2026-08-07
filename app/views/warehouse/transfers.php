<?php
// app/views/warehouse/transfers.php
$pageTitle = $data['title'] ?? 'طلبات نقل المخزون';
$transfers = $data['transfers'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'warehouse/transfers';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            <a href="<?php echo URLROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $pageTitle; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URLROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>المستودعات ونقل المخزون</span>
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
                    <h3><i class="fas fa-truck-ramp-box"></i> سجل حركات النقل الداخلي</h3>
                </div>
                <div>
                    <a href="<?php echo URLROOT; ?>/warehouse/create-transfer" class="btn-add">
                        <i class="fas fa-plus"></i> أمر نقل جديد
                    </a>
                </div>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>المرجع</th>
                                <th>المنتج المُنقول</th>
                                <th>مسار النقل (من ⟵ إلى)</th>
                                <th style="text-align:center;">الكمية</th>
                                <th>الحالة</th>
                                <th>التاريخ / المنفذ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transfers as $tr) : 
                                $statusClass = match($tr->status) {
                                    'completed' => 'badge-completed',
                                    'pending', 'approved' => 'badge-pending',
                                    'cancelled' => 'badge-cancelled',
                                    default => 'badge-pending'
                                };
                                $statusLabel = match($tr->status) {
                                    'completed' => 'مكتمل',
                                    'pending' => 'قيد الانتظار',
                                    'approved' => 'تمت الموافقة',
                                    'cancelled' => 'ملغي',
                                    default => $tr->status
                                };
                                $statusIcon = match($tr->status) {
                                    'completed' => 'check-double',
                                    'pending', 'approved' => 'clock',
                                    'cancelled' => 'xmark',
                                    default => 'circle'
                                };
                            ?>
                            <tr>
                                <td><span class="tr-num"><?php echo htmlspecialchars($tr->transfer_number); ?></span></td>
                                <td>
                                    <span class="prod-name"><i class="fas fa-box"></i> <?php echo htmlspecialchars($tr->product_name ?? '—'); ?></span>
                                </td>
                                <td>
                                    <div class="route-path">
                                        <span class="wh-from" title="المصدر"><?php echo htmlspecialchars($tr->from_warehouse_name ?? '—'); ?></span>
                                        <i class="fas fa-long-arrow-left"></i>
                                        <span class="wh-to" title="الوجهة"><?php echo htmlspecialchars($tr->to_warehouse_name ?? '—'); ?></span>
                                    </div>
                                </td>
                                <td style="text-align:center;"><span class="qty-val"><?php echo $tr->quantity; ?></span></td>
                                <td>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <i class="fas fa-<?php echo $statusIcon; ?>"></i> <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:12px; color:var(--text-muted);"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($tr->created_at)); ?></div>
                                    <div style="font-size:11px; color:var(--text-dark); margin-top:4px; font-weight:600;"><i class="fas fa-user-gear"></i> <?php echo htmlspecialchars($tr->requested_by_name ?? 'النظام'); ?></div>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($transfers)) : ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-arrows-left-right"></i>
                                        <h4>لا توجد عمليات نقل مخزون</h4>
                                        <p>قم بإنشاء أمر نقل لتحريك البضائع بين المستودعات والفروع</p>
                                        <a href="<?php echo URLROOT; ?>/warehouse/create-transfer" class="btn-add" style="display:inline-flex;">
                                            <i class="fas fa-plus"></i> نقل مخزون جديد
                                        </a>
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