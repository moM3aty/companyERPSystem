<?php
// app/views/warehouse/index.php
$pageTitle = $data['title'] ?? 'المستودعات';
$warehouses = $data['warehouses'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'warehouse/index';

$totalWarehouses = count($warehouses);
$mainWarehouses = 0;
$subWarehouses = 0;

foreach ($warehouses as $wh) {
    if ($wh->is_main) {
        $mainWarehouses++;
    } else {
        $subWarehouses++;
    }
}
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
                        <span>المشتريات والمخزون</span>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>المستودعات</span>
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

            <div class="summary-grid">
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-warehouse"></i></div>
                    <div>
                        <div class="sc-label">إجمالي المستودعات</div>
                        <div class="sc-value"><?php echo $totalWarehouses; ?></div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-building-circle-check"></i></div>
                    <div>
                        <div class="sc-label">مستودع رئيسي</div>
                        <div class="sc-value" style="color:var(--success);"><?php echo $mainWarehouses; ?></div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="sc-icon"><i class="fas fa-code-branch"></i></div>
                    <div>
                        <div class="sc-label">مستودع فرعي</div>
                        <div class="sc-value" style="color:var(--info);"><?php echo $subWarehouses; ?></div>
                    </div>
                </div>
            </div>

            <div class="toolbar">
                <div class="toolbar-right">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="ابحث باسم المستودع أو الكود..." autocomplete="off">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div>
                    <a href="<?php echo URLROOT; ?>/warehouse/create" class="btn-add">
                        <i class="fas fa-plus"></i> إضافة مستودع
                    </a>
                </div>
            </div>

            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>المستودع</th>
                                <th>النوع</th>
                                <th>تاريخ الإضافة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($warehouses as $wh) : ?>
                            <tr class="search-row" data-search="<?php echo htmlspecialchars($wh->name . ' ' . $wh->code); ?>">
                                <td style="color:var(--text-muted);font-weight:600;font-size:12px;"><?php echo $wh->id; ?></td>
                                <td><span class="wh-code"><?php echo htmlspecialchars($wh->code); ?></span></td>
                                <td>
                                    <span class="wh-name"><?php echo htmlspecialchars($wh->name); ?></span>
                                    <span class="wh-addr"><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($wh->address ?? 'لا يوجد عنوان مسجل'); ?></span>
                                </td>
                                <td>
                                    <?php if($wh->is_main): ?>
                                        <span class="badge badge-main"><i class="fas fa-star"></i> رئيسي</span>
                                    <?php else: ?>
                                        <span class="badge badge-sub">فرعي</span>
                                    <?php endif; ?>
                                </td>
                                <td><span style="font-size:12px;color:var(--text-muted);"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($wh->created_at)); ?></span></td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="<?php echo URLROOT; ?>/warehouse/edit/<?php echo $wh->id; ?>" class="act-btn btn-edit" title="تعديل" onclick="alert('واجهة التعديل غير متوفرة في التحديث الحالي')"><i class="fas fa-pen-to-square"></i></a>
                                        <form method="POST" action="<?php echo URLROOT; ?>/warehouse/delete/<?php echo $wh->id; ?>" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                            <button type="submit" class="act-btn btn-del" title="حذف" <?php echo $wh->is_main ? 'disabled style="opacity:0.3;cursor:not-allowed;"' : ''; ?>><i class="fas fa-trash-can"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($warehouses)) : ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-warehouse"></i>
                                        <h4>لا توجد مستودعات مسجلة</h4>
                                        <p>قم بإنشاء مستودع رئيسي لإدارة المخزون</p>
                                        <a href="<?php echo URLROOT; ?>/warehouse/create" class="btn-add" style="display:inline-flex;">
                                            <i class="fas fa-plus"></i> إضافة مستودع
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
        // البحث السريع
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.search-row');

        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                rows.forEach(row => {
                    const text = (row.getAttribute('data-search') || '').toLowerCase();
                    row.style.display = text.includes(q) ? '' : 'none';
                });
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